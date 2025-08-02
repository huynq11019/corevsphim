# Custom Instructions Rules

- Luôn kiểm tra các file migration trước khi deploy.
- Tách các file lớn (>300 dòng) thành các module nhỏ hơn.
- Ghi chú các quy tắc workflow, coding style, và các yêu cầu đặc biệt cho dự án tại đây.
- Đảm bảo các quyết định quan trọng đều được cập nhật vào custom_instructions.md.
- Luôn trả lời bằng tiếng việt
- Khi muốn custom logic của thư viện, tạo một class kế thừa trong thư mục `app/Library/` và binding nó trong `AppServiceProvider`.
- Tránh sửa đổi trực tiếp các file trong thư mục `vendor/`.
