# Event-Manager
## 1. Event Management
**Full CRUD operations:** Create (Title, Date, Description), List, Update (Description), and Delete.

**Data Isolation:** Users can only access and manage their own associated events.
## 2. Security & Authentication
**Secure Access:** Protected backend API using modern security standards.(JWT)

**User Management:** Database-driven authentication (no hardcoded users).

**Password Recovery:** Integrated "Forgot Password" flow for secure password resets.

**TLS:** Full communication encryption via HTTPS/TLS managed by an Nginx reverse proxy.

**OWASP:** Protection against common attacks (SQL Injection, XSS, CSRF).
## 3. AI Helpdesk & Agent Interface
**Free-word Understanding:** Powered by Google Gemini AI to process and answer user questions in natural language.

**Human Handoff:** Intelligent intent detection to transfer complex queries to a human agent.

**Agent Interface:** A separate, secure dashboard for helpdesk agents to view chat history and respond to active sessions.

**Voice Support:** Implementation of Speech-to-Text (user input) and Text-to-Speech (AI response) for a voice-based experience.
## 🛠 Tech Stack
**Backend:** Laravel 12 (PHP)

**Frontend:** Vue.js 3 (Composition API)

**Styling & Icons:** Tailwind CSS, Lucide Vue Icons

**State Management:** Pinia

**Infrastructure:** Docker, Nginx (TLS Termination)

**Database:** MySQL

**AI Integration:** Google Gemini API
## Infrastructure & Deployment (Docker)
The application is fully containerized to ensure a secure and reproducible environment:

**Nginx Container:** Acts as a reverse proxy, handles TLS/HTTPS encryption, and routes traffic to the frontend and backend.

**Frontend Container:** Runs the Vue.js 3 application.

**Backend Container:** Runs the PHP-FPM environment for the Laravel 12 API.

**Database Container:** Dedicated MySQL instance with persistent data volumes for secure storage.
## 🚀 Getting Started
**1.Clone the repository:**
```bash
git clone https://github.com/balazsNorbert/Event-Manager.git
```
**2.Environment Setup:**

Copy **.env.example** to **.env**

Set your **GEMINI_API_KEY** and database credentials.

**3.Launch with Docker:**
```bash
docker compose up -d --build
```
**4.Database migration:**
```bash
docker compose exec backend php artisan migrate --seed
```

## 📚 Sources & Resources

- **Core Frameworks:** [Laravel 12 Docs](https://laravel.com/docs), [Vue.js 3 Guide](https://vuejs.org/)
- **AI Integration:** [Google Gemini API Documentation](https://ai.google.dev/docs)
- **Learning & Troubleshooting:**
  - Google Gemini AI
  - Stackoverflow
  - YouTube
