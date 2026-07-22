</div> <!-- End .container -->
    </main> <!-- End .main-content -->

    <!-- Footer -->
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <p class="mb-0 text-muted small">
                    &copy; <?php echo date("Y"); ?> <strong>ELMS</strong>. All rights reserved.
                </p>
                <div class="text-muted small">
                    <span>Designed for modern HR & Employee workflows.</span>
                </div>
            </div>
        </div>
    </footer>
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
        </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>