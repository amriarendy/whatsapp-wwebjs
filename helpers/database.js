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

const getAutoreply = async (keyword) => {
  const connection = await createConnection();
  const [rows] = await connection.execute(
    "SELECT response FROM autoreplies WHERE keyword = ?",
    [keyword]
  );
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
