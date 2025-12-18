const express = require('express');
const app = express();
const port = 3000;

app.get('/', (req, res) => {
  res.send('<h1>Xin chao STU - Phien ban FEATURE</h1>');
  res.send(`
        <h1 style="color: red; text-align: center;">Thi Thứ Năm - Ca 4 - Nguyễn Bảo Kiệt</h1>
        <h2 style="text-align: center;">Project 1: Node.js Backend trên Render</h2>
        <p style="text-align: center;">Demo CI/CD tự động thành công!</p>
    `);
});

app.listen(port, () => {
  console.log(`App dang chay tai port ${port}`);
});