# User guide

This guide explains how to use **Organization Document Manager** as an end user: creating and managing documents, using reminders, chat, reports, and settings.

**Audience:** Organization members, document managers, and organization administrators.

---

## Table of contents

1. [Introduction](#introduction)
2. [Getting started](#getting-started)
3. [Documents](#documents)
4. [Reminders](#reminders)
5. [Chat (messages)](#chat-messages)
6. [Reports](#reports)
7. [Organization settings](#organization-settings)
8. [Roles and access](#roles-and-access)
9. [Language and calendar](#language-and-calendar)
10. [Frequently asked questions](#frequently-asked-questions)

---

## Introduction

Organization Document Manager lets you:

- **Create and manage documents** using organization-defined forms (with dynamic fields).
- **Set reminders** on date fields so you and others receive email notifications.
- **Communicate** via private messages, general organization chat, and support chat with system administrators.
- **View reports** and export document data (e.g. to Excel/CSV).
- **Customize** dashboard widgets and language (English / Persian).

Your access depends on your **role** (e.g. Viewer, Member, Document Manager, Organization Admin). Only organization admins can manage users and roles.

---

## Getting started

1. Log in at the **App** URL (e.g. `https://yoursite.com/app`).
2. Ensure your account is assigned to an **organization** and has at least one **role** (contact your administrator if not).
3. Use the sidebar to open **Documents**, **Messages**, **Reports**, or **Organization Settings** (if allowed).

---

## Documents

### What are documents?

Documents are records created from **forms**. Each form defines fields (text, numbers, dates, files, etc.). When you create a document, you fill in those fields; the result is stored as a document with a title and optional status.

### Creating a document

1. In the sidebar, go to **Documents**.
2. Click **Create** (or equivalent).
3. Choose the **form** (and version, if applicable).
4. Fill in the form fields. Required fields are marked.
5. Optionally set **reminders** in the Reminders section (see [Reminders](#reminders)).
6. Save. The document is created and linked to your organization.

### Editing and viewing documents

- From the documents list, open a document to **view** or **edit** it (if your role allows).
- You can update form data, attachments, status, and notes.
- **Created by** shows the user who created the document.

### Document status and permissions

- Documents can have a **status** (e.g. draft, submitted). The list can be filtered by status.
- Permissions (view, create, edit, delete) are controlled by your **role**. If you cannot see Create or Edit, your role does not include those permissions.

---

## Reminders

### What are reminders?

Reminders are **email notifications** sent on a chosen date (and optionally time), based on date fields in the form (e.g. “Due date”, “Review date”). Not all forms or date fields support reminders; your administrator enables them per form/field.

### Enabling a reminder when creating or editing a document

1. When filling the document form, find the **Reminders** section.
2. If the form has reminder-enabled date fields, you will see toggles and options for those fields.
3. Turn **Enable reminder** on for the date you want.
4. Optionally add **additional recipient** email addresses.
5. Save the document. The system will send emails at the scheduled time (server must have the [scheduler configured](INSTALL.md#scheduler-reminders)).

### Reminder calendar

- Use **Reminders** (or calendar page) in the sidebar to see reminders in a calendar view.
- You can see when reminders are due for documents you have access to.

---

## Chat (messages)

Access **Messages** from the sidebar to use the chat system.

### Private messages (direct)

- **Private** (or Direct) chat is one-to-one with another member of your organization.
- Search for a user and start a conversation. Only you and the recipient see these messages.

### General chat

- **General** chat is organization-wide. All members of your organization can see and participate.
- Use it for team discussions visible to everyone in the org.

### Support chat

- **Support** is for contacting **system administrators** (General Managers).
- Messages in Support are visible to admins who handle support for your organization.
- Only organization admins (or users with the right permissions) may see the Support channel in the app; check with your administrator.

### Real-time behavior

- Messages typically refresh automatically (e.g. by polling). Send a message and it appears for the other party once they refresh or when the page updates.

---

## Reports

### App reports (organization)

1. Open **Reports** in the sidebar.
2. You see **statistics** (e.g. total documents, documents this month) and **charts**, for example:
   - Documents by form  
   - Documents by month  
   - Documents by status  
   - Documents by creator  
   - Charts **by form fields** (one chart per field per form, when data exists)
3. Use **Download as Excel (CSV)** to export documents (with form data) for your organization. The file opens in Excel or similar tools.

All report data is **scoped to your organization**.

---

## Organization settings

If you have **organization admin** (or equivalent) rights:

1. Go to **Organization Settings** (or similar in the sidebar).
2. You can adjust **dashboard widgets** (e.g. which widgets are shown on the dashboard).
3. Save. Changes apply to how the dashboard looks for users in your organization.

---

## Roles and access

### Default roles (typical)

| Role | Typical permissions |
|------|----------------------|
| **Organization Admin** | Full access: documents, users, roles, chat, settings. |
| **Document Manager** | Manage documents and use chat; no user/role management. |
| **Member** | View, create, and edit documents; use chat; no delete documents or user/role management. |
| **Viewer** | View-only: documents, users, roles, chat. |

Exact permissions (e.g. `documents.view`, `documents.create`, `users.edit`) are defined by your administrator. If a menu item or action is missing, your role likely does not have the required permission.

---

## Language and calendar

- The interface supports **English** and **Persian (Farsi)**. Use the language switcher if available (e.g. in the header or settings).
- **Calendar type** (Gregorian vs. Persian/Jalali) can be configured in organization or user settings where available. Date fields in forms may show in the selected calendar.

---

## Frequently asked questions

### How do I enable a reminder?

When creating or editing a document, look for the **Reminders** section. If the form has date fields with reminders enabled by the administrator, you will see a toggle to enable a reminder and optional extra recipients.

### Who can see my private messages?

Only you and the recipient. System administrators do not see private (direct) messages; they only see messages sent to **Support**.

### Why can’t I see some forms when creating a document?

Forms must be **published** and **active**. Only administrators (in the admin panel) can publish and activate forms. If a form is missing, ask your organization admin or system administrator.

### Why can’t I access the Admin panel?

The **Admin** panel (e.g. `/admin`) is for **General Managers** only (system administrators). Regular organization users use the **App** panel (`/app`).

### Why don’t I receive reminder emails?

Reminders are sent by a scheduled task. The server must run `php artisan app:process-reminders` (e.g. every minute via cron). Also check that mail is configured correctly (see [INSTALL.md](INSTALL.md#optional-mail-and-queue)).

### Where can I get more technical or installation information?

- Installation and server setup: [INSTALL.md](INSTALL.md)  
- Technical and developer information: [ENGINEER.md](ENGINEER.md) and [ARCHITECTURE.md](ARCHITECTURE.md)
