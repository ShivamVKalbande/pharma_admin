<footer id="footer" class="footer">
    <div class="copyright">
        &copy; <?php echo date('Y'); ?> <strong><span>Urban Nest</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Secure Admin Panel v2.0
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/js/main.js"></script>

<style>
.footer {
    padding: 20px 0;
    font-size: 14px;
    transition: all 0.3s;
    border-top: 1px solid #cddfff;
    background: #fff;
    position: fixed;
    bottom: 0;
    left: 260px;
    right: 0;
}

.footer .copyright {
    text-align: center;
    color: #012970;
}

.footer .credits {
    padding-top: 5px;
    text-align: center;
    font-size: 13px;
    color: #012970;
}

@media (max-width: 1199px) {
    .footer {
        left: 0;
    }
}
</style>

</body>
</html>
