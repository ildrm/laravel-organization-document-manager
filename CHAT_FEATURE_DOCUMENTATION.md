# Chat Feature Documentation

## Overview

This document describes the improved chat functionality for the Document Management System. The chat system now supports both **Direct Messages** (private 1-on-1 conversations) and **General Chat** (organization-wide conversations).

## Key Features

### 1. Access Control (Fixed)

The chat feature is now automatically accessible to:
- **General Admin** (users with `is_general_manager = true`)
- **Organization Admin** (users with `is_org_admin = true`)
- **Regular Users** with explicit `chat.view` and `chat.send` permissions

**Before:** Only users with role-based permissions could access chat
**After:** All admin roles automatically get access regardless of their assigned roles

### 2. Direct Messages (NEW)

Users can now send private messages to individual organization members:

#### Features:
- **User Selection**: Search and select any user in your organization
- **Conversation History**: View previous conversations with each user
- **Last Message Preview**: See a preview of the last message sent/received
- **Real-time Updates**: Messages appear instantly with polling every 5 seconds
- **Read Status**: Track whether messages have been read
- **Conversation List**: Sidebar shows all active conversations sorted by most recent

### 3. General Chat

Organization-wide chat where all members can participate:

#### Features:
- **Public Discussion**: Messages visible to all organization members
- **User Identification**: Each message shows the sender's name and timestamp
- **Date Grouping**: Messages grouped by date for better organization
- **Message History**: Last 50 messages loaded by default

## Database Changes

### New Table: `private_chats`

```sql
CREATE TABLE private_chats (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  organization_id BIGINT NOT NULL,
  sender_id BIGINT NOT NULL,
  recipient_id BIGINT NOT NULL,
  message TEXT NOT NULL,
  is_read BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  FOREIGN KEY (organization_id) REFERENCES organizations(id),
  FOREIGN KEY (sender_id) REFERENCES users(id),
  FOREIGN KEY (recipient_id) REFERENCES users(id),
  
  INDEX (organization_id, sender_id),
  INDEX (organization_id, recipient_id)
);
```

### Fields:
- **id**: Unique message identifier
- **organization_id**: Links messages to the organization
- **sender_id**: User who sent the message
- **recipient_id**: User who receives the message
- **message**: Message content (max 1000 characters)
- **is_read**: Track read status (for future implementation)
- **created_at/updated_at**: Timestamps

## Models

### PrivateChat Model

Located: `app/Models/PrivateChat.php`

```php
class PrivateChat extends Model
{
    protected $fillable = [
        'organization_id',
        'sender_id',
        'recipient_id',
        'message',
        'is_read',
    ];

    public function sender(): BelongsTo
    public function recipient(): BelongsTo
    public function otherUser(int $userId): User
}
```

### User Model Updates

Added relationships:
```php
public function sentMessages()
{
    return $this->hasMany(PrivateChat::class, 'sender_id');
}

public function receivedMessages()
{
    return $this->hasMany(PrivateChat::class, 'recipient_id');
}
```

## Chat Controller: `Chat.php`

Located: `app/Filament/App/Pages/Chat.php`

### Key Methods:

1. **getAvailableUsers()**
   - Returns all users in the organization (except current user)
   - Supports live search filtering by name/email
   - Returns: `Collection`

2. **getConversations()**
   - Returns list of users with active conversations
   - Includes last message preview
   - Sorted by most recent activity
   - Returns: `Collection`

3. **getPrivateMessages()**
   - Fetches conversation between current user and selected recipient
   - Loads last 100 messages in chronological order
   - Includes sender and recipient information
   - Returns: `Collection`

4. **getGeneralMessages()**
   - Fetches organization-wide general chat messages
   - Loads last 50 messages
   - Returns: `Collection`

5. **sendMessage()**
   - Delegates to either `sendPrivateMessage()` or `sendGeneralMessage()` based on chat type
   - Validates message content (required, max 1000 chars)
   - Checks user permissions
   - Clears input and refreshes view after sending

6. **canAccess()**
   - Checks if user can access chat
   - Returns `true` for General/Organization admins
   - Otherwise checks `chat.view` permission

### Public Properties:

```php
#[Url] public string $type = 'private'; // 'private' or 'general'
#[Url] public ?int $recipient_id = null; // Selected recipient for private messages
public string $message = ''; // Current message being typed
public string $searchQuery = ''; // Search filter for users
```

## Blade View: `chat.blade.php`

Located: `resources/views/filament/app/pages/chat.blade.php`

### Layout Structure:

```
┌─────────────────────────────────────────┐
│          Chat Header                    │
├──────────────┬──────────────────────────┤
│   Sidebar    │                          │
│              │                          │
│   Users/     │   Message Area           │
│   Conversations
│              │                          │
│   - Search   │   - Messages             │
│   - Tabs     │   - Timestamps           │
│   - List     │   - Avatars              │
│              │                          │
├──────────────┼──────────────────────────┤
│   Input Area (spans both)                │
└─────────────────────────────────────────┘
```

### Key Components:

1. **Sidebar (Left Panel)**
   - Search box with live filtering
   - Two tabs: "Direct" and "General"
   - User/conversation list
   - Last message preview in conversations
   - Active selection highlighting

2. **Main Chat Area (Right Panel)**
   - Header with recipient info or channel name
   - Scrollable message container
   - Messages grouped by date
   - Avatars and user names
   - Own messages right-aligned (primary color)
   - Others' messages left-aligned (gray)

3. **Message Input**
   - Text input field with placeholder
   - Send button with loading state
   - Form submission via Livewire

### Messenger-Like UI Features:

- **Bubble Messages**: Round message bubbles like Telegram/WhatsApp
- **Avatar Badges**: User initials in colored circles
- **Time Stamps**: Shows message time (HH:mm format)
- **Date Separators**: Groups messages by date with centered labels
- **Auto-scroll**: Automatically scrolls to latest messages
- **Smooth Animations**: Fade-in animation for new messages
- **Active States**: Visual feedback for selected conversation/recipient
- **Dark Mode Support**: Full dark theme compatibility

## UI Improvements

### Before Issues:
- ❌ Big images loading (if any)
- ❌ No user selection - broadcast-only chat
- ❌ Not available for admin roles automatically
- ❌ Limited to general broadcasts

### After Improvements:
- ✅ No images - text-only messaging optimized
- ✅ Direct message capability with user selection
- ✅ Automatic access for admin roles
- ✅ Hybrid model: Direct + General chats
- ✅ Messenger-like interface
- ✅ Optimized performance with indexed queries
- ✅ Live search for users
- ✅ Conversation preview
- ✅ Real-time updates via polling
- ✅ Responsive design
- ✅ Dark mode support

## Translation Keys

### English (`lang/en/common.php`)

```php
'messages' => 'Messages',
'search_users' => 'Search users...',
'direct' => 'Direct',
'general' => 'General',
'no_conversations_yet' => 'No conversations yet. Start a new chat!',
'general_chat_info' => 'General chat is visible to all organization members',
'general_chat' => 'General Chat',
'organization_members' => 'All members',
'start_new_chat' => 'Start new chat',
'no_messages_in_conversation' => 'No messages in this conversation. Start chatting!',
'select_recipient' => 'Select a recipient',
'select_user_from_list' => 'Select a user from the list to start a conversation',
'you' => 'You',
'no_permission' => 'You do not have permission to perform this action',
```

### Persian (`lang/fa/common.php`)

```php
'messages' => 'پیام‌ها',
'search_users' => 'جستجوی کاربران...',
'direct' => 'مستقیم',
'general' => 'عمومی',
'no_conversations_yet' => 'هنوز مکالمه‌ای وجود ندارد. یک گفتگوی جدید شروع کنید!',
'general_chat_info' => 'گفتگوی عمومی برای تمام اعضای سازمان قابل رؤیت است',
'general_chat' => 'گفتگوی عمومی',
'organization_members' => 'تمام اعضا',
'start_new_chat' => 'شروع گفتگوی جدید',
'no_messages_in_conversation' => 'هیچ پیامی در این مکالمه وجود ندارد. شروع به گفتگو کنید!',
'select_recipient' => 'یک گیرنده را انتخاب کنید',
'select_user_from_list' => 'یک کاربر را از لیست انتخاب کنید تا یک مکالمه شروع شود',
'you' => 'شما',
'no_permission' => 'شما مجوز انجام این کار را ندارید',
```

## Usage Flow

### Starting a Direct Message:

1. Navigate to `/app/chat`
2. Click on "Direct" tab (default)
3. Search for a user or select from conversation list
4. Type your message in the input field
5. Click send or press Enter
6. Message appears in the chat window

### Starting a General Chat:

1. Navigate to `/app/chat`
2. Click on "General" tab
3. Type your message
4. Click send
5. Message appears in the shared chat

### Searching Users:

1. In the "Direct" tab, start typing in the search box
2. Results filter by name or email
3. Click on a user to start/continue conversation

## Permissions

The chat feature uses two permissions:

- **`chat.view`**: Allows user to access chat pages
- **`chat.send`**: Allows user to send messages

Both permissions are automatically granted to:
- General Admins
- Organization Admins

## Performance Considerations

### Database Indexing:

The `private_chats` table includes indexes on:
- `(organization_id, sender_id)` - Fast lookup of sent messages
- `(organization_id, recipient_id)` - Fast lookup of received messages

### Query Optimization:

- Messages loaded in batches (100 for direct, 50 for general)
- Eager loading with `with()` to prevent N+1 queries
- Latest messages fetched first, then reversed for chronological display

### Frontend Optimization:

- Polling every 5 seconds instead of real-time WebSockets
- No image uploads - text only
- Minimal JavaScript dependencies
- Efficient CSS with Tailwind utilities

## Future Enhancements

1. **Message Search**: Search within conversations
2. **Read Receipts**: Show when messages are read
3. **Typing Indicators**: Show when someone is typing
4. **Message Reactions**: Add emoji reactions to messages
5. **Message Editing/Deletion**: Edit or delete sent messages
6. **File Sharing**: Share files in chat
7. **WebSocket Integration**: Real-time message delivery
8. **Message Encryption**: End-to-end encryption
9. **Chat Notifications**: Browser/email notifications
10. **User Status**: Show user online/offline status

## Security Notes

- Messages are stored in plain text in database
- No encryption for messages (can be added in future)
- Access control via permission system
- Organization isolation via `organization_id`
- Message max length: 1000 characters
- SQL injection protected via Eloquent ORM

## Testing

### Manual Testing Checklist:

- [ ] General Admin can access chat
- [ ] Organization Admin can access chat
- [ ] Regular users with permission can access chat
- [ ] Users without permission get access denied
- [ ] Can search for users in direct chat
- [ ] Can select user and send message
- [ ] Messages appear in conversation
- [ ] Can switch between direct and general chat
- [ ] General messages visible to all organization members
- [ ] Conversations persist in sidebar
- [ ] Auto-scroll works for new messages
- [ ] Timestamps display correctly
- [ ] Date separators appear correctly
- [ ] Dark mode styling works
- [ ] Responsive design on mobile
- [ ] RTL (Persian) text alignment works correctly

## Files Modified/Created

### New Files:
- `database/migrations/2026_01_02_100000_create_private_chats_table.php`
- `app/Models/PrivateChat.php`
- `app/Filament/App/Pages/Chat.php` (replaced)
- `resources/views/filament/app/pages/chat.blade.php` (replaced)

### Modified Files:
- `app/Models/User.php` - Added relationships for private chats
- `lang/en/common.php` - Added new translation keys
- `lang/fa/common.php` - Added Persian translations

### Unchanged:
- `database/migrations/2026_01_02_073042_create_chat_messages_table.php` - Still supports general chat
- `app/Models/ChatMessage.php` - Unchanged
- `app/Filament/Admin/Pages/SupportChat.php` - Admin support chat (unchanged)

## Support

For issues or questions about the chat feature, refer to this documentation or check the inline comments in the code.
