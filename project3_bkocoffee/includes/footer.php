<footer class="site-footer">
    <div class="container footer-content">
        
        <div class="footer-column">
            <h3 class="footer-heading icon-heading"> KIET BAN CAFE</h3>
            <p class="footer-desc">
                Chuyên cung cấp các loại cà phê hạt chất lượng cao, từ truyền thống đến hiện đại. 
                Chúng tôi mang đến hương vị cà phê đích thực cho mọi khách hàng.
            </p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
        
        <div class="footer-column">
            <h3 class="footer-heading">LIÊN KẾT</h3>
            <ul class="footer-links">
                <li><a href="index.php"><i class="fas fa-chevron-right"></i> Trang Chủ</a></li>
                <li><a href="giohang.php"><i class="fas fa-chevron-right"></i> Giỏ Hàng</a></li>
                <li><a href="menu.php"><i class="fas fa-chevron-right"></i> Sản Phẩm</a></li>
                <li><a href="lienhe.php"><i class="fas fa-chevron-right"></i> Liên Hệ</a></li>
            </ul>
        </div>
        
        <div class="footer-column">
            <h3 class="footer-heading">LIÊN HỆ</h3>
            <ul class="contact-list">
                <li>
                    <i class="fas fa-map-marker-alt"></i> 
                    <span>123 Nguyễn Huệ, Q.1, TP.HCM</span>
                </li>
                <li>
                    <i class="fas fa-envelope"></i> 
                    <span>contact@kietbancafe.com</span>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i> 
                    <span>+84 123 456 789</span>
                </li>
                <li>
                    <i class="fas fa-clock"></i> 
                    <span>T2-CN: 9:00 - 21:00</span>
                </li>
            </ul>
        </div>
        
        <div class="footer-column">
            <h3 class="footer-heading">VỊ TRÍ CỬA HÀNG</h3>
            <div class="map-box">
                <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4967788971787!2d106.70249631533424!3d10.775311792321675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4b3330bcc9%3A0xb3ff69197b10ec4f!2zTmd1eeG7hW4gSHXhu4csIELhur9uIE5naMOqLCBRdeG6rW4gMSwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5oLCBWaWV0bmFt!5e0!3m2!1sen!2s!4v1638888888888!5m2!1sen!2s" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
        
    </div>
    
    <div class="footer-bottom">
        <div class="container bottom-flex">
            <p class="copyright-text">© <?php echo date("Y"); ?> KIET BAN CAFE. All Rights Reserved.</p>
            <div class="payment-methods">
                <span class="pay-tag">Thanh toán: </span>
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-paypal"></i>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'added'): ?>
    
    <div class="modal-overlay">
        <div class="modal-content">
            <a href="#" onclick="this.closest('.modal-overlay').style.display='none'; return false;" class="close-modal">&times;</a>
            
            <div class="modal-icon">
                <i class="fas fa-heart"></i>
            </div>
            
            <h2 class="modal-title">CẢM ƠN BẠN<br>ĐÃ CHỌN TÔI</h2>
            
            <p style="color: #888;">Món ngon đã nằm gọn trong giỏ hàng!</p>
            
            <a href="menu.php" class="btn-back-menu">
                <i class="fas fa-arrow-left"></i> Quay lại Menu
            </a>
        </div>
    </div>

    <script>
        if (history.replaceState) {
            var newUrl = window.location.href.replace(/[\?&]status=added/, '');
            history.replaceState(null, null, newUrl);
        }
    </script>
<?php endif; ?>
</footer>