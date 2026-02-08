<footer id="footer" class="footer">
    <div class="copyright">
        &copy; <?php echo date('Y'); ?> <strong><span>Pharma Admin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        <i class="bi bi-shield-check text-success"></i> Secure Pharmacy Management System v1.0
    </div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/chart.js/chart.umd.js"></script>
<script src="assets/js/main.js"></script>

<style>
.footer {
    padding: 20px 0;
    font-size: 14px;
    transition: all 0.3s;
    border-top: 1px solid #cddfff;
    background: #fff;
    margin-top: 50px;
    margin-left: 260px;
}

.footer .copyright {
    text-align: center;
    color: #012970;
    font-weight: 500;
}

.footer .credits {
    padding-top: 5px;
    text-align: center;
    font-size: 13px;
    color: #899bbd;
}

.footer .credits i {
    margin-right: 5px;
}

@media (max-width: 1199px) {
    .footer {
        margin-left: 0;
    }
}

/* Add some bottom padding to main content so footer doesn't overlap */
main {
    padding-bottom: 100px;
}
</style>

</body>
</html>