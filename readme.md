# WHATSAPP API BOOT

For More Documentation <a href="https://github.com/pedroslopez/whatsapp-web.js">whatsapp-web.js</a>

### create with nodejs (javascript)

- node 16.16.0
- npm 8.11.0

### How to use?

- Download or clone this repo
- Enter project directory
- Run `npm install` (download npm dependencies)
- Run `nodemon app` (start server)
- Open browser and type url http://localhost:3000
- Scan QR Code
- Enjoy! &#127808;

### How to send massege and file

- send message
- POST http://localhost:3000/send-message

```json
{
  "number": "628xxxxxxxx",
  "message": "Hi. This is from whatsapp api!"
}
```

- send media file in server
- POST http://localhost:3000/send-media

```json
{
  "number": "6282246686900",
  "caption": "Here is caption!"
}
```

- send media with browse and weblink
- POST http://localhost:3000/send-media

```json
{
  "number": "6282246686900",
  "caption": "Here is caption!",
  "file": "http://dataload.goican.id/upload/blogs/efafdf410b515e3f8a410fc6e51ad60d.JPG"
}
```

### Vendor

- jquery
- socket.io
- getbootstrap
- sb-admin

### databases

- mysql
- postgree

### Package Module

```json
  "dependencies": {
    "axios": "^1.2.1",
    "express": "^4.18.2",
    "express-fileupload": "^1.4.0",
    "express-validator": "^6.14.2",
    "http": "^0.0.1-security",
    "qrcode": "^1.5.1",
    "socket.io": "^4.5.4",
    "whatsapp-web.js": "^1.18.4"
  }
```

## Thanks &#128151;
