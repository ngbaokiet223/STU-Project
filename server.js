const express = require('express');
const app = express();
const port = 3000;

app.get('/', (req, res) => {
  res.send('<h1>Xin chao STU - Phien ban MAIN</h1>');
});

app.listen(port, () => {
  console.log(`App dang chay tai port ${port}`);
});