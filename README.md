
### Đề tài: Thiết kế và xây dựng hệ thống 
### quản lý task cho công ty outsourcing bằng Laravel
### Lớp: DF-FIT-2D002_Tên: Trần Văn Vủ Luân

## Lý do tại sao chọn đề tài:
#   Tính thực tiễn:
        Nhiều công ty nhận dự án từ khách hàng trong & ngoài nước;
        Quản lý tiến độ, phân chia công việc và phối hợp giữa các bộ phận là yếu tố then chốt;
        Hệ thống giúp chuẩn hóa quy trình làm việc, nâng cao hiệu quả.
#   Phù hợp với khóa học:
        PHP & MySQL:
            Xử lý logic nghiệp vụ, thao tác dữ liệu, thiết kế CSDL chuẩn hóa.
        Laravel:
            Xây dựng hệ thống backend chuẩn MVC;
            Hỗ trợ middleware + policy để phân quyền vai trò (Admin, PM, Staff, Client);
            Xử lý routing, form, validation, upload file, gửi email, ... đầy đủ.
        Blade (Frontend):
            Tạo giao diện quản trị rõ ràng, dễ dùng.
        Git:
            Quản lý phiên bản code, hỗ trợ làm việc nhóm.
        Khả năng mở rộng và phát triển:
            Hệ thống có thể được phát triển thêm như: tính năng quản lý thời gian, báo cáo hiệu suất nhân viên, tích hợp với hệ thống chat, hoặc triển khai dạng SaaS.

Từ những lý do trên, em nhận thấy đề tài phù hợp với năng lực hiện tại, có tiềm năng ứng dụng cao, và mang tính học thuật lẫn thực tiễn tốt.

## Chức năng chính của hệ thống:
       Dashboard thống kê: Hiển thị số lượng task theo trạng thái, biểu đồ tiến độ dự án.
       Quản lý tài khoản người dùng: CRUD user, phân quyền theo vai trò (Admin, Leader, Member, Client).
       Quản lý dự án: CRUD dự án, thống kê tiến độ, gán nhóm phụ trách.
       Quản lý task: CRUD task, phân loại (Bug, Feature, Improvement,...), gán người phụ trách, quản lý trạng thái (Waiting Confirm, In Progress, Review, Done), thiết lập deadline.
       Trao đổi trong task: Cho phép bình luận, đính kèm file trong mỗi task.

##  Tài liệu tham khảo:
       Laravel: https://laravel.com/docs.
       MySQL: https://dev.mysql.com/doc/refman/8.0/en/sql-statements.html hoặc tiếng Việt https://vietjack.com/sql/ hoặc https://sqlbolt.com/
#   Hệ thống thực tế: 
        Jira: https://www.atlassian.com/software/jira
        Redmine: https://www.redmine.org/
        Asana: https://asana.com/
#   Template: https://adminlte.io V3.2.0