# Chat Feature - Quick Reference Guide

## Access the Chat

**URL**: `http://localhost:8000/app/chat`

**Who Can Access**:
- ✅ General Admin (automatic)
- ✅ Organization Admin (automatic)
- ✅ Users with `chat.view` permission
- ✅ Users with `chat.send` permission (for sending)

---

## Using Direct Messages

### Start a Conversation

1. Go to Chat page
2. Make sure "Direct" tab is selected (left side)
3. Search for a user or select from conversation list
4. Type your message in the input field
5. Click send button or press Enter

### Search for Users

1. Click in the search box at the top of sidebar
2. Type user name or email
3. Results appear instantly
4. Click on a user to start/continue conversation

### View Conversation History

- All previous messages with a user appear in the main chat area
- Messages grouped by date
- Scroll up to see older messages

---

## Using General Chat

### Send a Message to Everyone

1. Go to Chat page
2. Click "General" tab (right side of "Direct" tab)
3. Type your message
4. Send (button or Enter key)
5. Message appears for all organization members

### View General Messages

- All organization members' messages appear in chronological order
- Messages grouped by date
- Shows sender name and timestamp
- Scroll up to see older messages (up to 50 previous)

---

## UI Elements

### Sidebar (Left Panel)

| Element | Purpose |
|---------|---------|
| Search Box | Find users to chat with |
| "Direct" Tab | Switch to private messages |
| "General" Tab | Switch to organization chat |
| User List | View conversations or available users |
| Last Message | Preview of most recent message |
| Timestamp | When last message was sent |

### Main Chat Area (Right Panel)

| Element | Purpose |
|---------|---------|
| Header | Shows recipient name or "General Chat" |
| Status | Shows "Online" indicator |
| Message Bubbles | Individual messages with time |
| Date Separators | Groups messages by date |
| Avatars | User initials in colored circle |
| Input Field | Type your message here |
| Send Button | Submit your message |

---

## Message Features

### Message Format

```
[Avatar] [User Name] [Time]
    [Message Text]
```

**Example**:
```
JD  John Doe  14:25
    Hi, how are you doing today?
```

### Your Messages vs Others

- **Your Messages**: Right-aligned, blue background
- **Others' Messages**: Left-aligned, gray background
- **Names**: Always shown (except your own in direct chat)
- **Times**: HH:MM format (24-hour)

### Message Limits

- Minimum: 1 character
- Maximum: 1000 characters
- Plain text only (no formatting)
- No file uploads
- No images
- No links (yet)

---

## Conversation Management

### Active Conversations

- Shown in left sidebar
- Most recent first
- Shows last message preview
- Shows time of last message
- Click to switch conversation

### Starting New Conversations

**Direct**:
1. Search for user by name/email
2. User appears in search results (if not already conversing)
3. Click to start chatting

**General**:
- Everyone in organization automatically included
- Start typing and send

### Switching Conversations

- Click on a user in the sidebar (Direct tab)
- Conversation changes instantly
- Message history loads automatically

---

## Real-Time Updates

### Auto-Refresh

- Chat updates automatically every 5 seconds
- New messages appear without page reload
- Scroll position maintained when at bottom

### Auto-Scroll

- Automatically scrolls to newest message when one arrives
- Only if you're already at the bottom
- Scroll up to read older messages without auto-scrolling

### Sending Messages

- Click send button or press Enter
- Message appears immediately in your view
- Clears input field automatically
- Shows in recipient's chat after 5-second poll (maximum)

---

## Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| Enter | Send message (in input field) |
| Shift+Enter | New line in message (not supported - use Enter to send) |
| Ctrl+K / Cmd+K | Search users (if future enhancement added) |
| Escape | Close search/cancel (if future enhancement added) |

---

## Troubleshooting

### No Users Shown in Sidebar

**Problem**: Empty user list when opening "Direct" tab

**Solution**:
- Start typing in search box to find users
- Users only appear if you have active conversations or are searching
- Make sure you're in the same organization as other users

---

### Messages Not Sending

**Problem**: Message doesn't appear after clicking send

**Solutions**:
- Check if message is empty - minimum 1 character required
- Check if message exceeds 1000 characters - use shorter message
- Check if you have `chat.send` permission
- Wait a moment and try again
- Reload page if stuck

---

### Can't See Other Users' Messages

**Problem**: Only your messages appear in General chat

**Solution**:
- Check if polling is working - wait 5 seconds
- Scroll down to see latest messages
- Make sure you're in "General" tab
- Reload page if still having issues

---

### Search Not Working

**Problem**: Searching for users doesn't show results

**Solutions**:
- Make sure you're in "Direct" tab
- Type at least 1 character
- Check spelling of user's name/email
- User must be in same organization
- Wait a moment for results to load

---

## Tips & Best Practices

1. **Use Direct Messages**: For private conversations
2. **Use General Chat**: For team announcements
3. **Search First**: Before starting new conversation
4. **Keep Messages Clear**: Maximum 1000 characters
5. **Check Timestamps**: To understand message flow
6. **Scroll Up**: To see conversation history
7. **Wait for Updates**: Give 5 seconds for new messages
8. **Use Admin Access**: Admins can see/manage all chats

---

## Security Notes

- ✅ Messages are private between sender and recipient (Direct)
- ✅ General messages visible only to organization members
- ✅ Message history persists in database
- ⚠️ Messages are plain text (not encrypted)
- ⚠️ No auto-delete of messages
- ✅ Access controlled by permissions

---

## Dark Mode

Chat automatically supports system dark mode:
- Dark background at night
- Light text on dark background
- Adjusted colors for readability
- Toggle in system settings

---

## Language Support

- **English**: Full support
- **Persian (Farsi)**: Full support with RTL text alignment
- Change language in user settings

---

## Mobile/Responsive

- Works on mobile phones
- Sidebar collapses on small screens
- Touch-friendly buttons
- Swipe to navigate between direct/general
- Keyboard optimized

---

## Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| Page loads slow | Browser cache | Clear cache, reload page |
| Messages disappear | Refresh happened | Check conversation again |
| Can't find user | Wrong organization | Verify user is in your org |
| Permission denied | Missing permissions | Admin must grant `chat.view`/`chat.send` |
| Timestamps wrong | Server timezone | Check server time settings |
| Text looks weird | Font rendering | Update browser or clear cache |

---

## Admin Features

### General Admins

- Automatic access to all chats
- Can see chats across all organizations
- Can message any user
- No permission setup needed

### Organization Admins

- Automatic access within organization
- Can message any user in their organization
- Can manage role permissions
- Can assign `chat.view` and `chat.send` permissions

---

## Data Stored

### Direct Messages
- Stored in `private_chats` table
- Includes: sender, recipient, message text, timestamp
- Readable: sender and recipient only
- Visible to: admins (future)

### General Messages
- Stored in `chat_messages` table
- Includes: sender, message text, timestamp, organization
- Readable: all organization members
- Visible to: organization members only

---

## Future Features (Planned)

- 📅 Message search within conversations
- 👁️ Read receipts (see if message is read)
- ✍️ Typing indicators
- 😊 Emoji reactions
- 📎 File sharing
- 🔒 Message encryption
- 🔔 Notifications
- 📝 Message editing
- 🗑️ Message deletion
- 🗂️ Conversation archiving

---

## Getting Help

1. **Check Documentation**: See CHAT_FEATURE_DOCUMENTATION.md
2. **Check Code Comments**: Read inline comments in Chat.php
3. **Contact Admin**: Ask organization admin for permission issues
4. **Check Logs**: Server logs may show errors
5. **Clear Cache**: Reload page with Ctrl+Shift+R

---

## Summary

The chat feature provides:
- ✅ **Direct Messages**: 1-on-1 conversations
- ✅ **General Chat**: Organization-wide discussions
- ✅ **Search**: Find users quickly
- ✅ **History**: View previous messages
- ✅ **Real-time**: Automatic updates every 5 seconds
- ✅ **Modern UI**: Messenger-like interface
- ✅ **Dark Mode**: Night-friendly theme
- ✅ **Mobile Ready**: Works on all devices
- ✅ **Secure**: Organization isolated, permission-based

Ready to chat! 🎉
