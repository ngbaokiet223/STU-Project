# 1. Chọn môi trường Node.js nhẹ nhất (Alpine)
FROM node:18-alpine

# 2. Tạo thư mục chứa code bên trong container
WORKDIR /app

# 3. Copy file cấu hình thư viện vào trước
COPY package*.json ./

# 4. Cài đặt thư viện
RUN npm install

# 5. Copy toàn bộ code dự án vào
COPY . .

# 6. Mở cổng 3000 (cổng mà server.js của bạn đang chạy)
EXPOSE 3000

# 7. Lệnh chạy ứng dụng
CMD ["node", "server.js"]