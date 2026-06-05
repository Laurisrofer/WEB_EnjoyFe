import urllib.request
import urllib.parse
import json
import http.cookiejar

cj = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

# 1. Login
login_data = urllib.parse.urlencode({
    'email': 'admin@enjoyfe.es', # need to find an admin email, maybe 'admin@enjoyfe.es'
    'password': 'admin' # typically 'admin' or '123456'
}).encode('utf-8')

req = urllib.request.Request('http://localhost/enjoyfe_web/acciones/login.php', data=login_data)
resp = opener.open(req)
print("Login status:", resp.status)

# 2. Call admin_stats.php
req2 = urllib.request.Request('http://localhost/enjoyfe_web/acciones/admin_stats.php')
resp2 = opener.open(req2)
print("Stats response:", resp2.read().decode('utf-8'))
