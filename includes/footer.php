    </div> <!-- .container -->
    <script>
    // Dark mode toggle
    const toggle = document.createElement('button');
    toggle.innerHTML = '🌙';
    toggle.className = 'theme-toggle';
    toggle.onclick = () => {
        document.body.classList.toggle('dark');
        toggle.innerHTML = document.body.classList.contains('dark') ? '☀️' : '🌙';
    };
    document.body.appendChild(toggle);
</script>
</body>
</html>