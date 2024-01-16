const { Client, MessageMedia, LocalAuth } = require("whatsapp-web.js");
const express = require("express");
const socketIO = require("socket.io");
const qrcode = require("qrcode");
const http = require("http");
const fs = require("fs");
const { phoneNumberFormatter } = require("./helpers/formatter");
const fileUpload = require("express-fileupload");
const axios = require("axios");
const { body, validationResult } = require("express-validator");
const { error } = require("console");
const db = require("./helpers/database");
const port = process.env.PORT || 3000;

const app = express();
const server = http.createServer(app);
const io = socketIO(server);

app.use(express.json());
app.use(
  express.urlencoded({
    extended: true,
  })
);

/**
 * BASED ON MANY QUESTIONS
 * Actually ready mentioned on the tutorials
 *
 * The two middlewares above only handle for data json & urlencode (x-www-form-urlencoded)
 * So, we need to add extra middleware to handle form-data
 * Here we can use express-fileupload
 */
app.use(
  fileUpload({
    debug: false,
  })
);

app.get("*", function (req, res) {
  res.redirect("http://127.0.0.1/wa-api/"); //ganti dengan url domain Anda
});

const sessions = [];
const SESSIONS_FILE = "./whatsapp-sessions.json";

const createSessionsFileIfNotExists = function () {
  if (!fs.existsSync(SESSIONS_FILE)) {
    try {
      fs.writeFileSync(SESSIONS_FILE, JSON.stringify([]));
      console.log("Sessions file created successfully.");
    } catch (err) {
      console.log("Failed to create sessions file: ", err);
    }
  }
};

createSessionsFileIfNotExists();

const setSessionsFile = function (sessions) {
  fs.writeFile(SESSIONS_FILE, JSON.stringify(sessions), function (err) {
    if (err) {
      console.log(err);
    }
  });
};

const getSessionsFile = function () {
  return JSON.parse(fs.readFileSync(SESSIONS_FILE));
};

const createSession = function (id, description) {
  console.log("Creating session: " + id);
  const client = new Client({
    restartOnAuthFail: true,
    puppeteer: {
      headless: true,
      args: [
        "--no-sandbox",
        "--disable-setuid-sandbox",
        "--disable-dev-shm-usage",
        "--disable-accelerated-2d-canvas",
        "--no-first-run",
        "--no-zygote",
        "--single-process", // <- this one doesn't works in Windows
        "--disable-gpu",
      ],
    },
    authStrategy: new LocalAuth({
      clientId: id,
    }),
  });

  client.on("message", async (msg) => {
    const key_id = id;
    const keyword = msg.body.toLowerCase();
    // const replyMessage = await db.getAutoreply(keyword);
    const replyMessage = await db.getAutoreply(keyword, key_id);

    if (replyMessage !== false) {
      msg.reply(replyMessage);
    } else if (msg.body == "!groups") {
      client.getChats().then((chats) => {
        const groups = chats.filter((chat) => chat.isGroup);

        if (groups.length == 0) {
          msg.reply("You have no group yet.");
        } else {
          let replyMsg = "*YOUR GROUPS*\n\n";
          groups.forEach((group, i) => {
            replyMsg += `ID: ${group.id._serialized}\nName: ${group.name}\n\n`;
          });
          replyMsg +=
            "_You can use the group id to send a message to the group._";
          msg.reply(replyMsg);
        }
      });
    } else if (msg.body == "!info") {
      msg.reply(key_id);
    }
  });

  client.initialize();

  client.on("connection", () => {
    io.emit("message", "Try connecting...");
  });

  client.on("qr", (qr) => {
    io.emit("message", "Connecting...");
    console.log("QR RECEIVED", qr);
    qrcode.toDataURL(qr, (err, url) => {
      io.emit("qr", { id: id, src: url });
      io.emit("message", { id: id, text: "QR Code received, scan please!" });
    });
  });

  client.on("ready", () => {
    io.emit("ready", { id: id });
    io.emit("message", { id: id, text: "Whatsapp is ready!" });

    const savedSessions = getSessionsFile();
    const sessionIndex = savedSessions.findIndex((sess) => sess.id == id);
    savedSessions[sessionIndex].ready = true;
    setSessionsFile(savedSessions);
  });

  client.on("authenticated", () => {
    io.emit("authenticated", { id: id });
    io.emit("message", { id: id, text: "Whatsapp is authenticated!" });
  });

  client.on("auth_failure", function () {
    io.emit("message", { id: id, text: "Auth failure, restarting..." });
  });

  client.on("disconnected", (reason) => {
    io.emit("message", { id: id, text: "Whatsapp is disconnected!" });
    client.destroy();
    client.initialize();

    // Menghapus pada file sessions
    const savedSessions = getSessionsFile();
    const sessionIndex = savedSessions.findIndex((sess) => sess.id == id);
    savedSessions.splice(sessionIndex, 1);
    setSessionsFile(savedSessions);

    io.emit("remove-session", id);
  });

  // Tambahkan client ke sessions
  sessions.push({
    id: id,
    description: description,
    client: client,
  });

  // Menambahkan session ke file
  const savedSessions = getSessionsFile();
  const sessionIndex = savedSessions.findIndex((sess) => sess.id == id);

  if (sessionIndex == -1) {
    savedSessions.push({
      id: id,
      description: description,
      ready: false,
    });
    setSessionsFile(savedSessions);
  }
  // Check number registered or not
  // const checkRegisteredNumber = async function (number) {
  //   const isRegistered = await client.isRegisteredUser(number);
  //   return isRegistered;
  // };
};

const init = function (socket) {
  const savedSessions = getSessionsFile();

  if (savedSessions.length > 0) {
    if (socket) {
      /**
       * At the first time of running (e.g. restarting the server), our client is not ready yet!
       * It will need several time to authenticating.
       *
       * So to make people not confused for the 'ready' status
       * We need to make it as FALSE for this condition
       */
      savedSessions.forEach((e, i, arr) => {
        arr[i].ready = false;
      });

      socket.emit("init", savedSessions);
    } else {
      savedSessions.forEach((sess) => {
        createSession(sess.id, sess.description);
      });
    }
  }
};

init();

// Socket IO
io.on("connection", function (socket) {
  init(socket);

  socket.on("create-session", function (data) {
    console.log("Create session: " + data.id);
    createSession(data.id, data.description);
  });
});

// Send message
app.post(
  "/send-message",
  [
    body("api_key").notEmpty(),
    body("token").notEmpty(),
    body("number").notEmpty(),
    body("message").notEmpty(),
  ],
  async (req, res) => {
    console.log(req);
    const errors = validationResult(req).formatWith(({ msg }) => {
      return msg;
    });

    if (!errors.isEmpty()) {
      return res.status(422).json({
        status: 422,
        message: `Unprocessable Entity: Wrong parameters! ${errors.mapped()}`,
      });
    }

    const number = phoneNumberFormatter(req.body.number);
    const message = req.body.message;
    const apiKey = req.body.api_key;
    const token = req.body.token;
    const key = await db.keyApi(token);

    const client = sessions.find((sess) => sess.id == apiKey)?.client;

    if (!client) {
      return res.status(422).json({
        status: 422,
        message: `Unprocessable Entity: The api key: ${apiKey} is not found!`,
      });
    }

    if (token == key) {
      // const isRegisteredNumber = await checkRegisteredNumber(number);

      // if (!isRegisteredNumber) {
      //   return res.status(422).json({
      //     status: false,
      //     message: "The number is not registered",
      //   });
      // }
      client
        .sendMessage(number, message)
        .then((response) => {
          res.status(200).json({
            status: 200,
            response: `Success: Sent message ${response}`,
          });
        })
        .catch((err) => {
          res.status(500).json({
            status: 500,
            response: `Internal Server error: Something went wrong ${err}`,
          });
        });
    } else {
      res.status(401).json({
        status: 401,
        message: "Unauthorized: Invalid token!",
      });
    }
  }
);

// Send media
app.post(
  "/send-media",
  [
    body("api_key").notEmpty(),
    body("number").notEmpty(),
    body("message").notEmpty(),
  ],
  async (req, res) => {
    const number = phoneNumberFormatter(req.body.number);
    const caption = req.body.message;
    const apiKey = req.body.api_key;
    const token = req.body.token;
    const key = await db.keyApi(token);

    const client = sessions.find((sess) => sess.id == apiKey)?.client;
    if (!client) {
      return res.status(422).json({
        status: 422,
        message: `Unprocessable Entity: The api key: ${apiKey} is not found!`,
      });
    }

    if (token == key) {
      // const isRegisteredNumber = await checkRegisteredNumber(number);

      // if (!isRegisteredNumber) {
      //   return res.status(422).json({
      //     status: false,
      //     message: "The number is not registered",
      //   });
      // }
      if (req.body.file) {
        const file = req.body.file;
        let mimetype;
        const attachment = await axios
          .get(file, {
            responseType: "arraybuffer",
          })
          .then((response) => {
            mimetype = response.headers["content-type"];
            return response.data.toString("base64");
          });

        const media = new MessageMedia(mimetype, attachment, "Media");
        client
          .sendMessage(number, media, {
            caption: caption,
          })
          .then((response) => {
            res.status(200).json({
              status: 200,
              response: response,
            });
          })
          .catch((err) => {
            res.status(500).json({
              status: 500,
              response: err,
            });
          });
      } else {
        const file = req.files.file;
        const media = new MessageMedia(
          file.mimetype,
          file.data.toString("base64"),
          file.name
        );

        client
          .sendMessage(number, media, {
            caption: caption,
          })
          .then((response) => {
            res.status(200).json({
              status: 200,
              response: `Success: Sent message ${response}`,
            });
          })
          .catch((err) => {
            res.status(500).json({
              status: 500,
              response: `Internal Server error: Something went wrong ${err}`,
            });
          });
      }
    } else {
      res.status(401).json({
        status: 401,
        message: "Unauthorized: Invalid token!",
      });
    }
  }
);

const findGroupByName = async function (name) {
  const group = await client.getChats().then((chats) => {
    return chats.find(
      (chat) => chat.isGroup && chat.name.toLowerCase() == name.toLowerCase()
    );
  });
  return group;
};

// Send message to group
// You can use chatID or group name, yea!
app.post(
  "/send-group-message",
  [
    body("api_key").notEmpty(),
    body("id").custom((value, { req }) => {
      if (!value && !req.body.name) {
        throw new Error("Invalid value, you can use `id` or `name`");
      }
      return true;
    }),
    body("message").notEmpty(),
  ],
  async (req, res) => {
    const errors = validationResult(req).formatWith(({ msg }) => {
      return msg;
    });

    if (!errors.isEmpty()) {
      return res.status(422).json({
        status: 422,
        message: `Unprocessable Entity: Wrong parameters. ${errors.mapped()}`,
      });
    }

    let chatId = req.body.id;
    const groupName = req.body.name;
    const message = req.body.message;
    const apiKey = req.body.api_key;
    const token = req.body.token;
    const key = await db.keyApi(token);

    const client = sessions.find((sess) => sess.id == apiKey)?.client;
    if (!client) {
      return res.status(422).json({
        status: 422,
        message: `Unprocessable Entity: The api key ${apiKey} is not found!`,
      });
    }

    if (token == key) {
      // Find the group by name
      if (!chatId) {
        const group = await findGroupByName(groupName);
        if (!group) {
          return res.status(422).json({
            status: 422,
            message:
              "Unprocessable Entity: No group found with name: " + groupName,
          });
        }
        chatId = group.id._serialized;
      }

      client
        .sendMessage(chatId, message)
        .then((response) => {
          res.status(200).json({
            status: 200,
            response: `Success: Sent messsage ${response}`,
          });
        })
        .catch((err) => {
          res.status(500).json({
            status: 500,
            response: `Internal Server error: Something went wrong ${err}`,
          });
        });
    } else {
      res.status(401).json({
        status: 401,
        message: "Unauthorized: Invalid token!",
      });
    }
  }
);

// Clearing message on spesific chat
app.post("/clear-message", [body("number").notEmpty()], async (req, res) => {
  const errors = validationResult(req).formatWith(({ msg }) => {
    return msg;
  });

  if (!errors.isEmpty()) {
    return res.status(422).json({
      status: 422,
      message: `Unprocessable Entity: ${errors.mapped()}`,
    });
  }

  const number = phoneNumberFormatter(req.body.number);

  const isRegisteredNumber = await checkRegisteredNumber(number);

  if (!isRegisteredNumber) {
    return res.status(422).json({
      status: 422,
      message: "Unprocessable Entity: The number is not registered",
    });
  }

  const chat = await client.getChatById(number);

  chat
    .clearMessages()
    .then((status) => {
      res.status(200).json({
        status: 200,
        response: `Success: ${status}`,
      });
    })
    .catch((err) => {
      res.status(500).json({
        status: 500,
        response: `Internal Server error: ${err}`,
      });
    });
});

server.listen(port, function () {
  console.log("App running on * http://localhost:" + port);
});
