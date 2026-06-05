import re

with open('gestion_usuarios.php', 'r', encoding='utf-8') as f:
    content = f.read()

script_pattern = re.compile(r'<script>(.*?)</script>', re.DOTALL)
match = script_pattern.search(content)

if match:
    js_content = match.group(1).strip()
    
    with open('recursos/js/gestion_usuarios.js', 'w', encoding='utf-8') as f:
        f.write(js_content)
        
    replacement = '<script src="recursos/js/gestion_usuarios.js"></script>'

    new_content = content[:match.start()] + replacement + content[match.end():]
    
    with open('gestion_usuarios.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print('gestion_usuarios.php extracted.')
else:
    print('Regex failed for gestion_usuarios.php')
