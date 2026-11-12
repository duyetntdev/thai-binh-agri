---
name: Analyze Project and Create Onboarding Docs
description: "Analyze this repository's business domain, application architecture, technology stack, and infrastructure, then create or update an evidence-based onboarding document for new team members."
argument-hint: "Optional: documentation output path or areas to emphasize"
agent: agent
---

# Phân tích dự án và tạo tài liệu onboarding

Hãy phân tích source code, cấu hình và tài liệu hiện có trong repository này, sau đó tạo hoặc cập nhật tài liệu onboarding cho thành viên mới.

## Đầu vào

Người dùng có thể cung cấp đường dẫn file đầu ra hoặc các chủ đề muốn ưu tiên: ${input:focus:Để trống để dùng phạm vi mặc định}

Nếu không có yêu cầu khác, ghi tài liệu vào `docs/PROJECT_ONBOARDING.md`. Nếu thư mục `docs/` chưa tồn tại, tạo thư mục. Không ghi đè tài liệu onboarding hiện có mà chưa đọc và giữ lại nội dung còn đúng.

## Cách phân tích

1. Đọc hướng dẫn trong repository như `AGENTS.md`, `.github/copilot-instructions.md`, cùng README và tài liệu liên quan trước khi phân tích.
2. Tự khám phá repository; không giả định tên framework, kiến trúc, module hay thành phần hạ tầng chỉ từ README. Đối chiếu mô tả với code và cấu hình hiện tại.
3. Ưu tiên kiểm tra các nguồn phù hợp, nếu có: manifest và lockfile của dependency, entry points, routes, controllers, services, models, migrations, frontend assets, tests, Docker/Compose, web server, CI/CD, deployment manifests, Makefile và ví dụ cấu hình môi trường.
4. Lần theo các luồng chính từ điểm vào đến logic nghiệp vụ, persistence, tích hợp ngoài và response. Chỉ mô tả kiến trúc hoặc luồng mà có thể xác nhận từ code/cấu hình.
5. Phân biệt rõ thông tin đã xác minh với suy luận. Nếu tài liệu cũ mâu thuẫn với code, ghi nhận mâu thuẫn và lấy implementation hiện tại làm căn cứ; không âm thầm khẳng định cả hai là đúng.
6. Không suy đoán về môi trường production, quy trình triển khai, vận hành, bảo mật hoặc quy mô nếu repository không cung cấp bằng chứng. Ghi rõ “Chưa xác định từ repository” khi cần.
7. Không đọc hoặc đưa vào tài liệu giá trị bí mật từ `.env`, credentials, private keys, tokens hay thông tin nhạy cảm khác. Chỉ mô tả tên biến và mục đích khi được xác định từ code hoặc file mẫu như `.env.example`.
8. Không sửa source code, cấu hình, dependency hay dữ liệu. Chỉ tạo/cập nhật tài liệu đầu ra đã chọn.

## Nội dung tài liệu

Viết bằng tiếng Việt, dễ đọc cho người mới nhưng chính xác về kỹ thuật. Điều chỉnh mục cho phù hợp với dự án; bỏ mục không áp dụng thay vì bịa nội dung. Bao gồm:

- **Tổng quan sản phẩm và nghiệp vụ:** mục tiêu, người dùng/actor, năng lực chính và thuật ngữ nghiệp vụ có bằng chứng.
- **Tech stack:** ngôn ngữ, framework, runtime, thư viện quan trọng, database/cache/queue, frontend, công cụ build và phiên bản khi manifest hoặc cấu hình xác nhận.
- **Kiến trúc ứng dụng:** entry points, các lớp/module chính, trách nhiệm, cách các phần gọi nhau, lưu trữ dữ liệu và tích hợp ngoài.
- **Cấu trúc repository:** các thư mục quan trọng và nơi thành viên mới thường cần tìm khi xử lý từng loại công việc.
- **Mô hình dữ liệu:** các entity/bảng và quan hệ quan trọng, dựa trên model/schema/migration.
- **Luồng nghiệp vụ tiêu biểu:** mô tả ngắn các luồng chính theo thứ tự, kèm code path liên quan.
- **Hạ tầng và cách chạy:** dịch vụ/container, vai trò, cổng và dependency giữa chúng theo cấu hình; các lệnh setup, chạy, test và build được xác nhận từ tài liệu hoặc scripts hiện có.
- **Cấu hình và tích hợp:** các biến môi trường cần biết theo tên, dịch vụ ngoài và cách cấu hình an toàn; tuyệt đối không ghi giá trị bí mật.
- **Kiểm thử và chất lượng:** framework kiểm thử, vị trí test và cách chạy; nêu điều chưa được xác minh nếu chưa chạy kiểm thử.
- **Lộ trình đọc code cho người mới:** đề xuất một thứ tự ngắn, gắn với các file thực tế.
- **Điểm cần xác nhận:** chỉ liệt kê mâu thuẫn, thiếu thông tin hoặc điểm chưa thể kết luận có ý nghĩa cho onboarding.

## Yêu cầu về bằng chứng và chất lượng

- Dẫn nguồn inline bằng đường dẫn tương đối trong repository, ưu tiên kèm số dòng khi có thể, ví dụ: `app/Models/Order.php` hoặc `docker-compose.yml`.
- Mọi nhận định cụ thể về công nghệ, luồng, hạ tầng hoặc cấu trúc phải có nguồn tương ứng. Không biến tên thư mục hay tuyên bố trong README thành bằng chứng cho hành vi chưa được kiểm tra.
- Không chép lại toàn bộ README hoặc liệt kê mọi file. Tập trung vào bức tranh giúp người mới hiểu dự án và tìm đúng nơi để bắt đầu.
- Dùng Mermaid cho sơ đồ nếu làm rõ kiến trúc hoặc luồng; chỉ thể hiện các quan hệ đã xác minh từ repository.
- Giữ hướng dẫn setup nhất quán với lệnh thực tế. Không chạy lệnh có tác dụng phụ như migrate, seed, deploy hoặc thay đổi dữ liệu chỉ để hoàn thiện tài liệu.
- Cuối tài liệu ghi thời điểm phân tích và phạm vi kiểm tra ở mức ngắn gọn; không tuyên bố đã chạy test/build nếu chưa chạy.

Sau khi viết tài liệu, kiểm tra lại các đường dẫn nguồn được trích dẫn và xác nhận chỉ file tài liệu đầu ra bị thay đổi. Trong phản hồi cuối, nêu đường dẫn file đã tạo/cập nhật, các nguồn chính đã đối chiếu và những điểm còn chưa xác minh.