    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Plateforme Pédagogique</h4>
                <p>SAE S301 - Gestion des groupes</p>
                <p>Université Paris-Saclay - IUT d'Orsay</p>
            </div>
            
            <div class="footer-section">
                <h4>Liens utiles</h4>
                <ul class="footer-links">
                    <li><a href="https://www.universite-paris-saclay.fr" target="_blank">Université Paris-Saclay</a></li>
                    <li><a href="https://www.iut-orsay.universite-paris-saclay.fr" target="_blank">IUT d'Orsay</a></li>
                    <li><a href="<?php echo isset($baseUrl) ? $baseUrl : BASE_URL; ?>?controller=auth&action=connexion">Connexion</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <p>Pour toute question ou problème :</p>
                <p>Contactez l'administration</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Université Paris-Saclay - Tous droits réservés</p>
        </div>
    </footer>
    <a class="icon-help" title="Signaler une erreur par email" href="mailto:support@univ-paris-saclay.fr?subject=Signalement%20erreur%20plateforme">?</a>

    <script>
    // Toggle sous-menu sidebar
    document.querySelectorAll('.submenu-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            var parent = this.closest('.has-submenu');
            parent.classList.toggle('open');
        });
    });
    </script>
</body>
</html>

