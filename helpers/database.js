const mysql = require("mysql2/promise");

const createConnection = async () => {
  return await mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "wa_blast",
  });
};

if (createConnection.connection) {
  console.log("Database Unconnected");
} else {
  console.log("Database Connected");
}

const getAutoreply = async (keyword, key_id) => {
  const connection = await createConnection();
  const [users] = await connection.execute("SELECT api_key, email FROM users WHERE api_key = ?", [key_id]);
  const [rows] = await connection.execute(`SELECT response, media, user_id FROM autoreplies WHERE keyword = '${keyword}' AND user_id = '${users[0].email}'`);
  if (rows.length > 0) return rows[0].response;
  return false;
};

const keyApi = async (token) => {
  const connection = await createConnection();
  const [row_key] = await connection.execute(
    "SELECT token FROM setting WHERE token = ?",
    [token]
  );
  if (row_key.length > 0) return row_key[0].token;
  return false;
};

module.exports = {
  createConnection,
  getAutoreply,
  keyApi,
};
