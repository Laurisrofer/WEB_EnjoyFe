import re

with open('horario.php', 'r', encoding='utf-8') as f:
    content = f.read()

script_pattern = re.compile(r'<script>(.*?)</script>', re.DOTALL)
match = script_pattern.search(content)

if match:
    js_content = match.group(1).strip()
    
    with open('recursos/js/horario.js', 'w', encoding='utf-8') as f:
        f.write(js_content)
        
    replacement = '<script src="recursos/js/horario.js"></script>'

    new_content = content[:match.start()] + replacement + content[match.end():]
    
    with open('horario.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print('horario.php extracted.')
else:
    print('Regex failed for horario.php')
