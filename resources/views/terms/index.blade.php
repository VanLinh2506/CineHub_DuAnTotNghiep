@extends('layouts.app')

@php
$current_page = 'terms';
$title = 'Điều khoản dịch vụ';
@endphp

@section('content')
<style>
.terms-wrapper {
    max-width: 860px;
    margin: 0 auto;
    padding: 3rem 1.5rem 5rem;
    color: var(--text-primary, #e0e0e0);
}
.terms-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.terms-header h1 {
    font-size: 2.2rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.5rem;
}
.terms-header .meta {
    color: #aaa;
    font-size: 0.9rem;
}
.terms-header .meta span {
    color: #ffc107;
    font-weight: 600;
}
.toc {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 1.5rem 2rem;
    margin-bottom: 2.5rem;
}
.toc h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #ffc107;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.toc ol {
    margin: 0;
    padding-left: 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.4rem 2rem;
}
.toc ol li a {
    color: #ccc;
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.2s;
}
.toc ol li a:hover {
    color: #e50914;
}
.terms-section {
    margin-bottom: 2.5rem;
    scroll-margin-top: 100px;
}
.terms-section h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e50914;
    display: flex;
    align-items: center;
    gap: 10px;
}
.terms-section h2 .num {
    background: #e50914;
    color: #fff;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.terms-section p,
.terms-section li {
    color: #ccc;
    line-height: 1.8;
    font-size: 0.95rem;
}
.terms-section ul,
.terms-section ol {
    padding-left: 1.5rem;
    margin: 0.8rem 0;
}
.terms-section li {
    margin-bottom: 0.5rem;
}
.terms-section strong {
    color: #fff;
}
.highlight-box {
    background: rgba(229, 9, 20, 0.08);
    border-left: 4px solid #e50914;
    border-radius: 0 8px 8px 0;
    padding: 1rem 1.2rem;
    margin: 1rem 0;
    font-size: 0.9rem;
    color: #ddd;
}
.highlight-box.info {
    background: rgba(255, 193, 7, 0.08);
    border-color: #ffc107;
}
.terms-footer {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    text-align: center;
    color: #888;
    font-size: 0.9rem;
}
.print-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.15);
    color: #ddd;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s;
    cursor: pointer;
    margin: 0 5px;
}
.print-btn:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
}
@media (max-width: 600px) {
    .toc ol { grid-template-columns: 1fr; }
    .terms-header h1 { font-size: 1.6rem; }
}
@media print {
    body { background: #fff; color: #000; }
    .terms-header h1, .terms-section h2 { color: #000; }
    .terms-section p, .terms-section li { color: #333; }
    .print-btn, nav, footer, .back-btn { display: none !important; }
}
</style>

<div class="terms-wrapper">

    <div class="terms-header">
        <h1><i class="fas fa-file-contract" style="color:#e50914;"></i> Điều khoản dịch vụ</h1>
        <div class="meta">
            Phiên bản <span>1.0</span> &nbsp;·&nbsp; Có hiệu lực từ <span>01/01/2025</span> &nbsp;·&nbsp; Cập nhật lần cuối: <span>07/2025</span>
        </div>
    </div>

    {{-- Mục lục --}}
    <div class="toc">
        <h3><i class="fas fa-list-ul"></i> Mục lục</h3>
        <ol>
            <li><a href="#section-1">Giới thiệu & Chấp nhận điều khoản</a></li>
            <li><a href="#section-2">Tài khoản người dùng</a></li>
            <li><a href="#section-3">Nội dung phân loại độ tuổi</a></li>
            <li><a href="#section-4">Gói đăng ký (Subscription)</a></li>
            <li><a href="#section-5">Đặt vé rạp & Hoàn tiền</a></li>
            <li><a href="#section-6">Bình luận & Đánh giá</a></li>
            <li><a href="#section-7">Thanh toán</a></li>
            <li><a href="#section-8">Quyền sở hữu trí tuệ</a></li>
            <li><a href="#section-9">Giới hạn trách nhiệm pháp lý</a></li>
            <li><a href="#section-10">Luật áp dụng & Giải quyết tranh chấp</a></li>
        </ol>
    </div>

    {{-- Điều 1 --}}
    <div class="terms-section" id="section-1">
        <h2><span class="num">1</span> Giới thiệu & Chấp nhận điều khoản</h2>
        <p>
            Chào mừng bạn đến với <strong>CineHub</strong> — nền tảng xem phim trực tuyến và đặt vé rạp chiếu phim tại Việt Nam.
            Bằng việc tạo tài khoản, truy cập hoặc sử dụng bất kỳ dịch vụ nào của CineHub, bạn xác nhận rằng bạn đã đọc,
            hiểu và <strong>đồng ý ràng buộc bởi</strong> các điều khoản dịch vụ này.
        </p>
        <p>
            Nếu bạn không đồng ý với bất kỳ điều khoản nào, vui lòng không sử dụng dịch vụ của chúng tôi.
        </p>
        <div class="highlight-box">
            <i class="fas fa-info-circle"></i> CineHub có quyền sửa đổi điều khoản này bất cứ lúc nào.
            Khi có thay đổi quan trọng, chúng tôi sẽ thông báo đến email đã đăng ký. Việc tiếp tục sử dụng dịch vụ
            sau khi điều khoản được cập nhật đồng nghĩa với việc bạn chấp nhận phiên bản mới.
        </div>
    </div>

    {{-- Điều 2 --}}
    <div class="terms-section" id="section-2">
        <h2><span class="num">2</span> Tài khoản người dùng</h2>
        <p>Để sử dụng đầy đủ dịch vụ, bạn cần tạo tài khoản. Khi đăng ký, bạn cam kết:</p>
        <ul>
            <li>Cung cấp thông tin trung thực, chính xác và đầy đủ (họ tên, email, ngày sinh).</li>
            <li>Tự chịu trách nhiệm bảo mật mật khẩu và mọi hoạt động xảy ra dưới tài khoản của bạn.</li>
            <li>Thông báo ngay cho CineHub nếu phát hiện tài khoản bị truy cập trái phép.</li>
            <li>Mỗi cá nhân chỉ được tạo <strong>một tài khoản</strong>. Việc tạo nhiều tài khoản có thể dẫn đến khóa toàn bộ.</li>
        </ul>
        <p>CineHub có quyền tạm khóa hoặc chấm dứt tài khoản nếu phát hiện vi phạm điều khoản mà không cần thông báo trước.</p>
        <div class="highlight-box info">
            <i class="fas fa-shield-alt"></i> Mỗi tài khoản được giới hạn <strong>một phiên đăng nhập</strong> đồng thời.
            Khi đăng nhập từ thiết bị mới, hệ thống sẽ gửi mã OTP xác thực qua email.
        </div>
    </div>

    {{-- Điều 3 --}}
    <div class="terms-section" id="section-3">
        <h2><span class="num">3</span> Nội dung phân loại độ tuổi</h2>
        <p>CineHub áp dụng hệ thống phân loại nội dung theo độ tuổi theo quy định của pháp luật Việt Nam:</p>
        <ul>
            <li><strong>P (Mọi lứa tuổi):</strong> Phù hợp với tất cả khán giả.</li>
            <li><strong>T13 (Từ 13 tuổi trở lên):</strong> Có thể chứa nội dung nhẹ về bạo lực hoặc ngôn từ không phù hợp trẻ em.</li>
            <li><strong>T16 (Từ 16 tuổi trở lên):</strong> Có nội dung phức tạp về bạo lực, tình cảm người lớn.</li>
            <li><strong>T18 (Từ 18 tuổi trở lên):</strong> Chỉ dành cho người trưởng thành. Yêu cầu xác minh độ tuổi.</li>
        </ul>
        <p>
            Bạn tự chịu trách nhiệm cung cấp thông tin ngày sinh chính xác khi đăng ký.
            <strong>CineHub không chịu trách nhiệm pháp lý</strong> nếu bạn cung cấp ngày sinh sai để truy cập nội dung
            không phù hợp với độ tuổi thực tế.
        </p>
        <p>Phụ huynh và người giám hộ nên giám sát việc sử dụng dịch vụ của trẻ em và thanh thiếu niên.</p>
    </div>

    {{-- Điều 4 --}}
    <div class="terms-section" id="section-4">
        <h2><span class="num">4</span> Gói đăng ký (Subscription)</h2>
        <p>CineHub cung cấp nhiều gói đăng ký với mức giá và quyền lợi khác nhau, bao gồm gói <strong>Free</strong> (miễn phí)
            và các gói trả phí cho phép xem nội dung cao cấp.</p>
        <ul>
            <li>Phí đăng ký được thanh toán trước và <strong>không hoàn lại</strong> sau khi đã kích hoạt, trừ trường hợp lỗi kỹ thuật từ phía CineHub.</li>
            <li>CineHub có quyền thay đổi mức giá gói đăng ký với thông báo trước <strong>tối thiểu 30 ngày</strong> qua email.</li>
            <li>Khi bạn vi phạm điều khoản, CineHub có quyền chấm dứt gói đăng ký mà không hoàn tiền.</li>
            <li>Quyền lợi gói đăng ký chỉ áp dụng cho tài khoản cá nhân, không được chuyển nhượng hoặc chia sẻ.</li>
        </ul>
    </div>

    {{-- Điều 5 --}}
    <div class="terms-section" id="section-5">
        <h2><span class="num">5</span> Đặt vé rạp & Hoàn tiền</h2>
        <p>Khi đặt vé rạp thông qua CineHub, bạn đồng ý với các điều khoản sau:</p>
        <ul>
            <li>Vé đã mua chỉ được hoàn tiền khi hủy <strong>trước ít nhất 2 giờ</strong> so với giờ chiếu <strong>VÀ</strong> vé chưa được sử dụng (chưa quét mã QR tại rạp).</li>
            <li>Vé đã quét mã QR (đã vào rạp) <strong>không thể hủy</strong> và không được hoàn tiền.</li>
            <li>Nếu suất chiếu bị hủy bởi rạp (sự cố kỹ thuật, thay đổi lịch chiếu), bạn sẽ được <strong>hoàn tiền đầy đủ</strong>.</li>
            <li>Thời gian xử lý hoàn tiền tối đa <strong>7 ngày làm việc</strong> về phương thức thanh toán gốc.</li>
            <li>Trường hợp lỗi hệ thống từ phía CineHub, hoàn tiền sẽ được xử lý trong vòng <strong>24 giờ</strong>.</li>
        </ul>
        <div class="highlight-box">
            <i class="fas fa-ticket-alt"></i> Vui lòng kiểm tra kỹ thông tin suất chiếu, số ghế, ngày giờ
            trước khi xác nhận thanh toán. CineHub không chịu trách nhiệm về những sai sót do người dùng gây ra.
        </div>
    </div>

    {{-- Điều 6 --}}
    <div class="terms-section" id="section-6">
        <h2><span class="num">6</span> Bình luận & Đánh giá</h2>
        <p>CineHub cho phép người dùng đăng bình luận và đánh giá phim. Bạn cam kết không đăng nội dung:</p>
        <ul>
            <li>Có tính chất thù địch, phân biệt đối xử về dân tộc, tôn giáo, giới tính, v.v.</li>
            <li>Spam, quảng cáo trái phép hoặc thông tin lừa đảo.</li>
            <li>Vi phạm bản quyền, bí mật cá nhân hoặc quyền riêng tư của người khác.</li>
            <li>Nội dung khiêu dâm, bạo lực không phù hợp với nền tảng.</li>
            <li>Thông tin sai lệch gây hiểu nhầm về nội dung phim hoặc CineHub.</li>
        </ul>
        <p><strong>Mức xử phạt theo thang bậc:</strong></p>
        <ol>
            <li>Cảnh cáo lần đầu — Nội dung bị ẩn, nhắc nhở qua thông báo.</li>
            <li>Tạm khóa tính năng bình luận <strong>7 ngày</strong>.</li>
            <li>Khóa tài khoản <strong>vĩnh viễn</strong> nếu tiếp tục vi phạm.</li>
        </ol>
        <p>
            CineHub có quyền xóa, ẩn hoặc gắn cờ bất kỳ nội dung vi phạm mà <strong>không cần thông báo trước</strong>.
            Bạn tự chịu trách nhiệm pháp lý về nội dung bình luận và đánh giá mình đăng tải.
        </p>
    </div>

    {{-- Điều 7 --}}
    <div class="terms-section" id="section-7">
        <h2><span class="num">7</span> Thanh toán</h2>
        <p>CineHub chấp nhận các phương thức thanh toán sau:</p>
        <ul>
            <li><strong>VNPay:</strong> Cổng thanh toán điện tử, hỗ trợ thẻ ATM nội địa, thẻ quốc tế Visa/Mastercard.</li>
            <li><strong>Điểm CineHub (Points):</strong> Điểm thưởng tích lũy từ hoạt động sử dụng dịch vụ, có thể dùng để thanh toán một phần hoặc toàn bộ đơn hàng.</li>
        </ul>
        <p>
            Bạn có trách nhiệm đảm bảo tài khoản thanh toán có đủ số dư khi thực hiện giao dịch.
            CineHub không lưu trữ thông tin thẻ ngân hàng của bạn — tất cả giao dịch được xử lý qua cổng thanh toán bảo mật của bên thứ ba.
        </p>
        <p>
            Trong trường hợp tranh chấp thanh toán, vui lòng liên hệ bộ phận hỗ trợ CineHub
            trong vòng <strong>30 ngày</strong> kể từ ngày phát sinh giao dịch.
        </p>
    </div>

    {{-- Điều 8 --}}
    <div class="terms-section" id="section-8">
        <h2><span class="num">8</span> Quyền sở hữu trí tuệ</h2>
        <p>
            Toàn bộ nội dung trên CineHub bao gồm phim, hình ảnh, logo, giao diện, mã nguồn và tài liệu
            đều thuộc quyền sở hữu của CineHub hoặc đối tác cung cấp nội dung, được bảo vệ bởi
            pháp luật về sở hữu trí tuệ Việt Nam và quốc tế.
        </p>
        <ul>
            <li>Nghiêm cấm sao chép, phân phối, phát tán, tải xuống hoặc khai thác nội dung dưới bất kỳ hình thức nào khi chưa có sự cho phép bằng văn bản.</li>
            <li>Việc sử dụng bất hợp pháp có thể dẫn đến hậu quả pháp lý nghiêm trọng.</li>
            <li>Bạn được phép chia sẻ liên kết đến nội dung trên CineHub cho mục đích cá nhân, phi thương mại.</li>
        </ul>
    </div>

    {{-- Điều 9 --}}
    <div class="terms-section" id="section-9">
        <h2><span class="num">9</span> Giới hạn trách nhiệm pháp lý</h2>
        <p>CineHub cung cấp dịch vụ theo nguyên tắc "nguyên trạng" và không bảo đảm dịch vụ luôn hoạt động liên tục, không có lỗi. CineHub không chịu trách nhiệm về:</p>
        <ul>
            <li>Thiệt hại gián tiếp, ngẫu nhiên phát sinh từ việc sử dụng hoặc không thể sử dụng dịch vụ.</li>
            <li>Mất mát dữ liệu do sự cố kỹ thuật ngoài tầm kiểm soát.</li>
            <li>Nội dung bình luận, đánh giá do người dùng tạo ra.</li>
            <li>Hành vi của người dùng khác trên nền tảng.</li>
            <li>Thiệt hại phát sinh từ việc người dùng cung cấp thông tin sai lệch (bao gồm ngày sinh).</li>
        </ul>
        <p>Trách nhiệm tối đa của CineHub trong mọi trường hợp không vượt quá số tiền bạn đã thanh toán cho dịch vụ trong vòng 30 ngày gần nhất.</p>
    </div>

    {{-- Điều 10 --}}
    <div class="terms-section" id="section-10">
        <h2><span class="num">10</span> Luật áp dụng & Giải quyết tranh chấp</h2>
        <p>
            Điều khoản dịch vụ này được điều chỉnh và giải thích theo <strong>pháp luật Việt Nam</strong>.
        </p>
        <p>
            Mọi tranh chấp phát sinh liên quan đến dịch vụ CineHub sẽ được ưu tiên giải quyết
            thông qua thương lượng và hòa giải thiện chí. Nếu không đạt được thỏa thuận,
            tranh chấp sẽ được đưa ra giải quyết tại <strong>Tòa án nhân dân có thẩm quyền tại Việt Nam</strong>.
        </p>
        <div class="highlight-box info">
            <i class="fas fa-envelope"></i> Để liên hệ hỗ trợ hoặc gửi khiếu nại, vui lòng truy cập
            mục <strong>Hỗ trợ</strong> trên website hoặc gửi email đến địa chỉ hỗ trợ của CineHub.
        </div>
    </div>

    {{-- Footer --}}
    <div class="terms-footer">
        <p style="margin-bottom:1rem;">Phiên bản 1.0 · Có hiệu lực từ 01/01/2025</p>
        <button onclick="window.print()" class="print-btn">
            <i class="fas fa-print"></i> In trang
        </button>
        <a href="{{ route('home') }}" class="print-btn">
            <i class="fas fa-home"></i> Về trang chủ
        </a>
    </div>

</div>
@endsection
