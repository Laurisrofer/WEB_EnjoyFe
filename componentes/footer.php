<!-- Footer Global EnjoyFe -->
<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-links">
            <a href="cookies.php">Política de cookies</a>
            <span class="footer-separator">·</span>
            <a href="aviso_legal.php">Aviso legal</a>
            <span class="footer-separator">·</span>
            <a href="contacto.php">Contacto</a>
        </div>
        <div class="footer-copy">
            © <?php echo date('Y'); ?> EnjoyFe — Todos los derechos reservados
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background: var(--card-bg);
        border-top: 1px solid var(--border-color);
        padding: 18px 30px;
        text-align: center;
        font-size: 0.82em;
        color: var(--text-muted);
        position: fixed;
        bottom: 0;
        right: 0;
        width: <?php echo isset($_SESSION['token']) ? 'calc(100% - 250px)' : '100%'; ?>;
        z-index: 900;
        box-sizing: border-box;
    }
    /* Fallback variables if not in dashboard */
    :root {
        --card-bg: #ffffff;
        --border-color: #e0e0e0;
        --text-muted: #7f8c8d;
        --primary-color: #3498db;
    }
    @media (max-width: 768px) {
        .site-footer {
            width: 100%;
        }
    }
    .footer-content {
        display: flex;
        flex-direction: column;
        gap: 6px;
        align-items: center;
    }
    .footer-links {
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .footer-links a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.2s;
    }
    .footer-links a:hover {
        color: var(--primary-color);
        text-decoration: underline;
    }
    .footer-separator {
        color: var(--border-color);
    }
    .footer-copy {
        font-size: 0.9em;
        opacity: 0.7;
    }
</style>
