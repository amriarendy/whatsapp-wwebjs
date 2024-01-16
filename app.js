const { Client, LocalAuth, MessageMedia } = require('whatsapp-web.js');
const express = require('express');
const { body, ValidationResult, validationResult } = require('express-validator');
const socketIO = require('socket.io');
const qrcode = require('qrcode');
const http = require('http');
const axios = require('axios');
const { phoneNumberFormater } = require('./helpers/formatter');
const fileUpload = require('express-fileupload');
const { response } = require('express');

const app = express();
const server = http.createServer(app);
const io = socketIO(server);
const port = 3000;

// express parsing data
app.use(express.json());
app.use(express.urlencoded({extended: true}));
app.use(fileUpload({
    debug: true
}));

// main route
app.get('/', (req, res) => {
    res.sendFile('index.html', {root:__dirname});
});

// session. have 3 session in documentation NoAuth, LocalAuth and LocalSessionAuth
const client = new Client({
    restartOnAuthFail: true,
    puppeteer: {
      headless: true,
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--single-process', // <- this one doesn't works in Windows
        '--disable-gpu'
      ],
    },
    authStrategy: new LocalAuth()
});

// content message whatsapp
client.on('message', msg => {
    if (msg.body == '!ping') {
      msg.reply('pong');
    } else if (msg.body == 'good morning') {
      msg.reply('selamat pagi');
    } else if (msg.body == '!groups') {
      client.getChats().then(chats => {
        const groups = chats.filter(chat => chat.isGroup);
  
        if (groups.length == 0) {
          msg.reply('You have no group yet.');
        } else {
          let replyMsg = '*YOUR GROUPS*\n\n';
          groups.forEach((group, i) => {
            replyMsg += `ID: ${group.id._serialized}\nName: ${group.name}\n\n`;
          });
          replyMsg += '_You can use the group id to send a message to the group._'
          msg.reply(replyMsg);
        }
      });
    }
  
    // NOTE!
    // UNCOMMENT THE SCRIPT BELOW IF YOU WANT TO SAVE THE MESSAGE MEDIA FILES
    // Downloading media
    // if (msg.hasMedia) {
    //   msg.downloadMedia().then(media => {
    //     // To better understanding
    //     // Please look at the console what data we get
    //     console.log(media);
  
    //     if (media) {
    //       // The folder to store: change as you want!
    //       // Create if not exists
    //       const mediaPath = './downloaded-media/';
  
    //       if (!fs.existsSync(mediaPath)) {
    //         fs.mkdirSync(mediaPath);
    //       }
  
    //       // Get the file extension by mime-type
    //       const extension = mime.extension(media.mimetype);
          
    //       // Filename: change as you want! 
    //       // I will use the time for this example
    //       // Why not use media.filename? Because the value is not certain exists
    //       const filename = new Date().getTime();
  
    //       const fullFilename = mediaPath + filename + '.' + extension;
  
    //       // Save to file
    //       try {
    //         fs.writeFileSync(fullFilename, media.data, { encoding: 'base64' }); 
    //         console.log('File downloaded successfully!', fullFilename);
    //       } catch (err) {
    //         console.log('Failed to save the file:', err);
    //       }
    //     }
    //   });
    // }
  });

client.initialize();

// socket.io
io.on('connection', function(socket) {
    socket.emit('message', 'Connecting...');
    // generate QR Code
    client.on('qr', (qr) => {
        console.log('QR RECEIVED:', qr);
        // qrcode.generate(qr, {small: true});
        qrcode.toDataURL(qr, (err, url) => {
            socket.emit('qr', url);
            socket.emit('message', 'QR Code received, scan please!');
        });
    });
    // message client on session
    client.on('ready', () => {
        socket.emit('ready', 'Whatsapp is ready!');
        socket.emit('message', 'Whatsapp is ready!');
    });
});

const checkRegisteredNumber = async function(number) {
    const isRegistered = await client.isRegisteredUser(number);
    return isRegistered;
};

// send message
app.post('/send-message', [
    body('number').notEmpty(),
    body('message').notEmpty(),
    ], async(req, res) => {
        const errors = validationResult(req).formatWith(({msg }) => {
            return msg;
        });
        if (!errors.isEmpty()) {
            return res.status(422).json({
                status: false,
                message: errors.mapped()
            });
        }
        const number = phoneNumberFormater(req.body.number);
        const message = req.body.message;

        const isRegisteredNumber = await checkRegisteredNumber(number);
        if (!isRegisteredNumber) {
            return res.status(422).json({
                status: false,
                message: 'The number is not registered'
            });
        }

    client.sendMessage(number, message).then(response => {
        res.status(200).json({
            status: true,
            response: response
        })
    }).catch(err => {
        res.status(500).json({
            status: false,
            response: err
        });
    });
});

// send media 
app.post('/send-media', async (req, res) => {
    const number = phoneNumberFormater(req.body.number);
    const caption = req.body.caption;
    // 1. script untuk upload file yang ada di server (npm module yang digunakan whatsapp-web.js (MessageMedia))
    // const media = MessageMedia.fromFilePath('./man.png');

    // 2. script untuk upload file yang dari form input dan dijadikan base64 (npm module yang digunakan express-fileupload)
    // const file = req.files.file;
    // const media = new MessageMedia(file.mimetype, file.data.toString('base64'), file.name);

    // 3. script untuk upload file yang ada dari website lain menggunakan (npm module yang digunakan axios)
    const fileUrl = req.body.file;
    let mimetype;
    const attachment = await axios.get(fileUrl, {responseType: 'arraybuffer'}).then(response => {
        mimetype = response.headers['content-type'];
        return response.data.toString('base64');
    });
    const media = new MessageMedia(mimetype, attachment, 'Media');

    client.sendMessage(number, media, {caption: caption}).then(response => {
        res.status(200).json({
            status: true,
            response: response
        })
    }).catch(err => {
        res.status(500).json({
            status: false,
            response: err
        });
    });
});

// Send message to group
// You can use chatID or group name, yea!
app.post('/send-group-message', [
    body('id').custom((value, { req }) => {
      if (!value && !req.body.name) {
        throw new Error('Invalid value, you can use `id` or `name`');
      }
      return true;
    }),
    body('message').notEmpty(),
  ], async (req, res) => {
    const errors = validationResult(req).formatWith(({
      msg
    }) => {
      return msg;
    });
  
    if (!errors.isEmpty()) {
      return res.status(422).json({
        status: false,
        message: errors.mapped()
      });
    }
  
    let chatId = req.body.id;
    const groupName = req.body.name;
    const message = req.body.message;
  
    // Find the group by name
    if (!chatId) {
      const group = await findGroupByName(groupName);
      if (!group) {
        return res.status(422).json({
          status: false,
          message: 'No group found with name: ' + groupName
        });
      }
      chatId = group.id._serialized;
    }
  
    client.sendMessage(chatId, message).then(response => {
      res.status(200).json({
        status: true,
        response: response
      });
    }).catch(err => {
      res.status(500).json({
        status: false,
        response: err
      });
    });
  });

// Clearing message on spesific chat
app.post('/clear-message', [
    body('number').notEmpty(),
  ], async (req, res) => {
    const errors = validationResult(req).formatWith(({
      msg
    }) => {
      return msg;
    });
  
    if (!errors.isEmpty()) {
      return res.status(422).json({
        status: false,
        message: errors.mapped()
      });
    }
  
    const number = phoneNumberFormatter(req.body.number);
  
    const isRegisteredNumber = await checkRegisteredNumber(number);
  
    if (!isRegisteredNumber) {
      return res.status(422).json({
        status: false,
        message: 'The number is not registered'
      });
    }
  
    const chat = await client.getChatById(number);
    
    chat.clearMessages().then(status => {
      res.status(200).json({
        status: true,
        response: status
      });
    }).catch(err => {
      res.status(500).json({
        status: false,
        response: err
      });
    })
  });

// messege info server in running with express
server.listen(port, () => {
    console.log(`Server up running | http://localhost:${port}`);
});