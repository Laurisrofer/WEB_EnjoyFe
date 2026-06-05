import codecs
with codecs.open('acciones/editar_guia.php', 'r', 'utf-16') as f:
    content = f.read()

content = content.replace(
    'llamar_api("/academico/guia-docente/" . urlencode($id_asignatura), "PUT", null);\n$http_code = $resultado_api[\'http_code\'];\n$respuesta = $resultado_api[\'respuesta\'];\n$resp_data = $resultado_api[\'data\'];',
    'llamar_api("/academico/guia-docente/" . urlencode($id_asignatura), "PUT", json_decode($json_recibido, true));\n$httpCode = $resultado_api[\'http_code\'];\n$response = $resultado_api[\'respuesta\'];'
)
content = content.replace(
    'llamar_api("/academico/guia-docente/" . urlencode($id_asignatura), "PUT", null);\r\n$http_code = $resultado_api[\'http_code\'];\r\n$respuesta = $resultado_api[\'respuesta\'];\r\n$resp_data = $resultado_api[\'data\'];',
    'llamar_api("/academico/guia-docente/" . urlencode($id_asignatura), "PUT", json_decode($json_recibido, true));\r\n$httpCode = $resultado_api[\'http_code\'];\r\n$response = $resultado_api[\'respuesta\'];'
)

with codecs.open('acciones/editar_guia.php', 'w', 'utf-16') as f:
    f.write(content)
