import re
import os

with open('mensajeria.php', 'r', encoding='utf-8') as f:
    content = f.read()

script_pattern = re.compile(r'<script>(.*?)</script>', re.DOTALL)
match = script_pattern.search(content)

if match:
    js_content = match.group(1).strip()
    
    # Replace PHP injection
    js_content = js_content.replace('const miUsuario = "<?php echo htmlspecialchars($_SESSION[\'nombre_usuario\'] ?? \'\', ENT_QUOTES, \'UTF-8\'); ?>";', 'const miUsuario = window.EnjoyfeConfig.nombreUsuario;')
    
    os.makedirs('recursos/js', exist_ok=True)
    with open('recursos/js/mensajeria.js', 'w', encoding='utf-8') as f:
        f.write(js_content)
        
    replacement = '''<script>
    window.EnjoyfeConfig = window.EnjoyfeConfig || {};
    window.EnjoyfeConfig.nombreUsuario = "<?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?>";
</script>
<script src="recursos/js/mensajeria.js"></script>'''

    new_content = content[:match.start()] + replacement + content[match.end():]
    
    with open('mensajeria.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print('mensajeria.php extracted.')
else:
    print('Regex failed for mensajeria.php')
