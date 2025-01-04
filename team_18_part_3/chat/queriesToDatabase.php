<?php
$status = session_status();
if ($status == PHP_SESSION_NONE) {
    session_start();
}

include "../databaseAccess.php";

$conn = connect();

// display all chats
$query01 = 'SELECT 
                chat.`Chat Name`,
                message.`Message Text` AS RecentMessage,
                message.`Message Date`,
                message.`Message Time`,
                CASE
                    WHEN grp.`Group ID` IS NOT NULL THEN
                        (SELECT employee.`Employee First Name`
                        FROM `message-sender table` AS messageSender
                        JOIN `employee table` AS employee ON messageSender.`Sender ID` = employee.`Employee ID`
                        WHERE messageSender.`Message ID` = message.`Message ID`)
                    ELSE NULL
                END AS SenderName,
                notification.`Notification Read`
            FROM 
                `chat table` AS chat
            JOIN 
                `chat-message table` AS chatMessage ON chat.`Chat ID` = chatMessage.`Chat ID`
            JOIN 
                `message table` AS message ON chatMessage.`Message ID` = message.`Message ID`
            LEFT JOIN 
                `group table` AS grp ON chat.`Chat ID` = grp.`Group ID`
            LEFT JOIN 
                `notification table` AS notification ON notification.`Notification ID` = message.`Message ID`;
            WHERE
                message.`Message ID` = (
                    SELECT
                        MAX(`Message ID`)
                    FROM
                        `chat-message table` AS chatRecentMessage
                    WHERE
                        chatRecentMessage.`Chat ID` = chat.`Chat ID`
                )
            AND
                chatRecipient.`Chat Recipients ID` = $current_employee_id';

// display all individual chats
$query02 = 'SELECT 
                chat.`Chat Name`,
                message.`Message Text` AS RecentMessage,
                message.`Message Date`,
                message.`Message Time`,
                notification.`Notification Read`
            FROM 
                `chat table` AS chat
            JOIN 
                `chat-message table` AS chatMessage ON chat.`Chat ID` = chatMessage.`Chat ID`
            JOIN 
                `message table` AS message ON chatMessage.`Message ID` = message.`Message ID`
            LEFT JOIN 
                `group table` AS grp ON chat.`Chat ID` = grp.`Group ID`
            LEFT JOIN 
                `notification table` AS notification ON notification.`Notification ID` = message.`Message ID`
            WHERE 
                grp.`Group ID` IS NULL
            AND
                message.`Message ID` = (
                    SELECT
                        MAX(`Message ID`)
                    FROM
                        `chat-message table` AS chatRecentMessage
                    WHERE
                        chatRecentMessage.`Chat ID` = chat.`Chat ID`
                )
            AND
                chatRecipient.`Chat Recipients ID` = $current_employee_id';

// display all group chats
$query03 = 'SELECT 
                chat.`Chat Name`,
                message.`Message Text` AS RecentMessage,
                message.`Message Date`,
                message.`Message Time`,
                (SELECT employee.`Employee First Name`
                FROM `message-sender table` AS messageSender
                JOIN `employee table` AS employee ON messageSender.`Sender ID` = employee.`Employee ID`
                WHERE messageSender.`Message ID` = message.`Message ID`) AS SenderName,
                notification.`Notification Read`
            FROM 
                `chat table` AS chat
            JOIN 
                `chat-message table` AS chatMessage ON chat.`Chat ID` = chatMessage.`Chat ID`
            JOIN 
                `message table` AS message ON chatMessage.`Message ID` = message.`Message ID`
            JOIN 
                `group-chat table` AS groupChat ON chat.`Chat ID` = groupChat.`Chat ID`
            LEFT JOIN 
                `notification table` AS notification ON notification.`Notification ID` = message.`Message ID`
            WHERE
                message.`Message ID` = (
                    SELECT
                        MAX(`Message ID`)
                    FROM
                        `chat-message table` AS chatRecentMessage
                    WHERE
                        chatRecentMessage.`Chat ID` = chat.`Chat ID`
                )
            AND
                chatRecipient.`Chat Recipients ID` = $current_employee_id';

// display all unread chats
$query04 = 'SELECT
                chat.`Chat Name`,
                message.`Message Text` AS RecentMessage,
                message.`Message Date`,
                message.`Message Time`,
                (SELECT employee.`Employee First Name`
                FROM `message-sender table` AS messageSender
                JOIN `employee table` AS employee ON messageSender.`Sender ID` = employee.`Employee ID`
                WHERE messageSender.`Message ID` = message.`Message ID`) AS SenderName,
                notification.`Notification Read`
            FROM 
                `chat table` AS chat
            JOIN 
                `chat-message table` AS chatMessage ON chat.`Chat ID` = chatMessage.`Chat ID`
            JOIN 
                `message table` AS message ON chatMessage.`Message ID` = message.`Message ID`
            LEFT JOIN 
                `notification table` AS notification ON notification.`Notification ID` = message.`Message ID`
            WHERE 
                notification.`Notification Read` = 0
            AND
                message.`Message ID` = (
                    SELECT
                        MAX(`Message ID`)
                    FROM
                        `chat-message table` AS chatRecentMessage
                    WHERE
                        chatRecentMessage.`Chat ID` = chat.`Chat ID`
                )
            AND
                chatRecipient.`Chat Recipients ID` = [current_employee_id]';

// display all favourited chats
$query05 = 'SELECT 
                chat.`Chat Name`,
                message.`Message Text` AS RecentMessage,
                message.`Message Date`,
                message.`Message Time`,
                (SELECT employee.`Employee First Name`
                FROM `message-sender table` AS messageSender
                JOIN `employee table` AS employee ON messageSender.`Sender ID` = employee.`Employee ID`
                WHERE messageSender.`Message ID` = message.`Message ID`) AS SenderName,
                notification.`Notification Read`
            FROM 
                `chat table` AS chat
            JOIN 
                `chat-message table` AS chatMessage ON chat.`Chat ID` = chatMessage.`Chat ID`
            JOIN 
                `message table` AS message ON chatMessage.`Message ID` = message.`Message ID`
            JOIN 
                `chat-chat recipients table` AS chatRecipients ON chat.`Chat ID` = chatRecipients.`Chat ID`
            LEFT JOIN 
                `notification table` AS notification ON notification.`Notification ID` = message.`Message ID`
            WHERE 
                chatRecipients.`Chat Favourited` = 1
            AND
                message.`Message ID` = (
                    SELECT
                        MAX(`Message ID`)
                    FROM
                        `chat-message table` AS chatRecentMessage
                    WHERE
                        chatRecentMessage.`Chat ID` = chat.`Chat ID`
                )
            AND
                chatRecipient.`Chat Recipients ID` = [current_employee_id]';

// display chats ordered by newest chat first
$query06 = 'ORDER BY 
                message.Message Date DESC, message.Message Time DESC'; // add this to the end of all the queries

// display chats ordered by oldest chat first
$query07 = 'ORDER BY 
                message.Message Date ASC, message.Message Time ASC'; // add this to the end of all the queries

// display chats from search term
$query08 = 'WHERE 
                chat.`Chat Name` LIKE '%user_input%''; // add this to the end of each query

// favourite a chat
$query09 = "UPDATE `chat-chat recipients table` 
            SET `Chat Favourited` = 1 
            WHERE `Chat ID` = $chat_id AND `Chat Recipients ID` = $employee_id";

// unfavourite a chat
$query10 = "UPDATE `chat-chat recipients table` 
            SET `Chat Favourited` = 0
            WHERE `Chat ID` = $chat_id AND `Chat Recipients ID` = $employee_id";

// mute a chat
$query11 = "UPDATE `chat-chat recipients table` 
            SET `Chat Muted` = 1
            WHERE `Chat ID` = $chat_id AND `Chat Recipients ID` = $employee_id";

// unmute a chat
$query12 = "UPDATE `chat-chat recipients table` 
            SET `Chat Muted` = 0
            WHERE `Chat ID` = $chat_id AND `Chat Recipients ID` = $employee_id";

// start a chat
$query13 = "SELECT `Employee ID` 
            FROM `employee table` 
            WHERE `Employee Email` = '$email'";
$query14 = "INSERT INTO `chat table` (`Chat Name`)
            VALUES ('$chatName')";
$query15 = "INSERT INTO `chat-chat recipients table` (`Chat ID`, `Chat Recipients ID`, `Chat Muted`)
            VALUES ($chatID, $currentEmployeeID, 0)";
$query16 = "INSERT INTO `chat-chat recipients table` (`Chat ID`, `Chat Recipients ID`, `Chat Muted`)
            VALUES ($chatID, $selectedEmployeeID, 0)";

// start a group chat
$query17 = "INSERT INTO `group table` (`Group Name`, `Group Description`, `Group Created Date`, `Group Created Time`)
            VALUES ('$group_name', '$group_description', '$group_created_date', '$group_created_time')";
$query18 = "INSERT INTO `group-admin table` (`Group ID`, `Admin ID`)
            VALUES ($group_id, $admin_employee_id)";
$query19 = "INSERT INTO `chat table` (`Chat Name`)
            VALUES ('$group_name')";
$query20 = "INSERT INTO `chat-chat recipients table` (`Chat ID`, `Chat Recipients ID`, `Chat Muted`)
            VALUES ($chat_id, $admin_employee_id, 0)";
$query21 = "INSERT INTO `group-chat table` (`Group ID`, `Chat ID`)
            VALUES ($group_id, $chat_id)";

// delete a chat
$query22 = "SELECT `Chat ID` 
            FROM `chat-chat recipients table` 
            WHERE `Chat Recipients ID` = $currentEmployeeID";
$query23 = "DELETE FROM `chat-chat recipients table` 
            WHERE `Chat ID` = $chatID";
$query24 = "DELETE FROM `chat-message table` 
            WHERE `Chat ID` = $chatID";
$query25 = "DELETE FROM `chat table`
            WHERE `Chat ID` = $chatID";

// leave a group
$query26 = "SELECT `Chat ID` 
            FROM `group-chat table`
            WHERE `Group ID` = $groupID";
$query27 = "DELETE FROM `chat-chat recipients table`
            WHERE `Chat ID` = $chatID
            AND `Chat Recipients ID` = $employeeID";

// delete a group as an admin
$query28 = "SELECT COUNT(*) 
            FROM `group-admin table`
            WHERE `Group ID` = $groupID 
            AND `Admin ID` = $employeeID";
$query29 = "DELETE FROM `message-sender table`
            WHERE `Message ID` IN (SELECT `Message ID` 
                                    FROM `chat-message table` 
                                    WHERE `Chat ID` = $groupID)";
$query30 = "DELETE FROM `message-receiver table` 
            WHERE `Message ID` IN (SELECT `Message ID` 
                                    FROM `chat-message table` 
                                    WHERE `Chat ID` = $groupID)";
$query31 = "DELETE FROM `message table`
            WHERE `Message ID` IN (SELECT `Message ID` 
                                    FROM `chat-message table` 
                                    WHERE `Chat ID` = $groupID)";
$query32 = "DELETE FROM `chat-message table`
            WHERE `Chat ID` = $groupID";
$query33 = "DELETE FROM `chat-chat recipients table`
            WHERE `Chat ID` = $groupID";
$query34 = "DELETE FROM `group-chat table`
            WHERE `Group ID` = $groupID";
$query35 = "DELETE FROM `group-admin table`
            WHERE `Group ID` = $groupID";
$query36 = "DELETE FROM `group table`
            WHERE `Group ID` = $groupID";

// sending invitations to a group chat
$query37 = "SELECT COUNT(*) 
            FROM `group-admin table`
            WHERE `Admin ID` = $adminID
            AND `Group ID` = $groupID";
$query38 = "INSERT INTO `invitation table` (`Invitation Text`)
            VALUES ('$invitationText')";
$query39 = "INSERT INTO `invitation-admin table` (`Invitation ID`, `Admin ID`)
            VALUES ($invitationID, $adminID)";
$query40 = "SELECT `Employee ID`
            FROM `employee table`
            WHERE `Employee Email` = '$recipientEmail'";
$query41 = "INSERT INTO `invitation-recipients table` (`Invitation ID`, `Recipient ID`, `Invitation Accepted`)
            VALUES ($invitationID, $recipientID, NULL)";

// accept invitation to a group chat
$query42 = "SELECT `Invitation ID`
            FROM `invitation-recipients table`
            WHERE `Recipient ID` = '$currentEmployeeId'
            AND `Invitation Accepted` IS NULL";
$query43 = "SELECT `Invitation Text`
            FROM `invitation table`
            WHERE `Invitation ID` = '$invitationId'";
$query44 = "UPDATE `invitation-recipients table`
            SET `Invitation Accepted` = 1
            WHERE `Invitation ID` = '$invitationId' AND `Recipient ID` = '$currentEmployeeId'";
$query45 = "SELECT `Admin ID`
            FROM `invitation-admin table`
            WHERE `Invitation ID` = '$invitationId'";
$query46 = "SELECT `Group ID`
            FROM `group-admin table`
            WHERE `Admin ID` = '$adminId'";
$query47 = "SELECT `Chat ID`
            FROM `group-chat table`
            WHERE `Group ID` = '$groupId'";
$query48 = "INSERT INTO `chat-chat recipients table` (`Chat ID`, `Chat Recipients ID`, `Chat Muted`)
            VALUES ('$chatId', '$currentEmployeeId', 0)";
$query49 = "DELETE FROM `invitation-recipients table`
            WHERE `Invitation ID` = '$invitationId'
            AND `Recipient ID` = '$currentEmployeeId'";

// decline invitation to a group chat
$query50 = $query42;
$query51 = $query43;
$query52 = $query49;

// delete invitation if it no longer exists in invitation-recipients table
$query53 = "SELECT 1
            FROM `invitation-recipients table`
            WHERE `Invitation ID` = '$invitationId'";
$query54 = "DELETE FROM `invitation-admin table`
            WHERE `Invitation ID` = '$invitationId'";
$query55 = "DELETE FROM `invitation table`
            WHERE `Invitation ID` = '$invitationId'";

// create message
$query56 = "INSERT INTO `message table` (`Message Text`, `Message Media`, `Message Date`, `Message Time`, `Message Drafted`)
            VALUES ('$messageText', '$messageMedia', '$messageDate', '$messageTime', 0)";
$query57 = "INSERT INTO `chat-message table` (`Chat ID`, `Message ID`)
            VALUES ($chatId, $messageId)";
$query58 = "INSERT INTO `message-sender table` (`Message ID`, `Sender ID`)
            VALUES ($messageId, $senderId)";
$query59 = "SELECT `Chat Recipients ID` 
            FROM `chat-chat recipients table`
            WHERE `Chat ID` = $chatId
            AND `Chat Recipients ID` != $senderId";
$query60 = "INSERT INTO `message-receiver table` (`Message ID`, `Receiver ID`)
            VALUES ($messageId, $recipientId)";

// delete message
$query61 = "DELETE FROM `message-receiver table`
            WHERE `Message ID` = $messageId";
$query62 = "DELETE FROM `chat-message table`
            WHERE `Message ID` = $messageId";
$query63 = "DELETE FROM `message-sender table`
            WHERE `Message ID` = $messageId";
$query64 = "DELETE FROM `message table`
            WHERE `Message ID` = $messageId";

// draft a message
$query65 = "INSERT INTO `message table` (Message_Text, Message_Media, Message_Drafted) 
            VALUES ('$messageText', '$messageMedia', TRUE)";
$query66 = "INSERT INTO `Chat message table` (Chat_ID, Message_ID)
            VALUES ('$chatID', '$messageID')";
$query67 = "INSERT INTO `message-sender table` (Message_ID, Sender_ID)
            VALUES ('$messageID', '$senderID')";

// load a drafted message
$query68 = "SELECT message.*
            FROM `message table` AS message
            JOIN `message-sender table` AS messageSender ON message.`Message ID` = messageSender.`Message ID`
            JOIN `chat-message table` AS chatMessage ON message.`Message ID` = chatMessage.`Message ID`
            JOIN `chat-chat recipients table` AS chatChatRecipients ON chatMessage.`Chat ID` = chatChatRecipients.`Chat ID`
            WHERE message.`Message Drafted` = TRUE
            AND messageSender.`Sender ID` = '$currentEmployeeID'
            AND chatChatRecipients.`Chat Recipient ID` = '$currentEmployeeID'";

// send a drafted message
$query69 = "UPDATE Message
            SET `Message Text` = '$newMessageText',
                `Message Media` = '$newMessageMedia',
                `Message Date` = '$currentDate',
                `Message Time` = '$currentTime',
                `Message Drafted` = FALSE
            WHERE `Message ID` = $chosenMessageID";
$query70 = "INSERT INTO Message_Receiver (Message_ID, Receiver_ID)
            VALUES ($chosenMessageID, $currentEmployeeID)";

// view messages
$query71 = "SELECT 
                message.`Message Text`,
                message.`Message Media`,
                message.`Message Date`,
                message.`Message Time`,
                employee.`Employee First Name` AS SenderName
            FROM 
                `chat-message table` AS chatMessage
            JOIN 
                `message table` AS message ON chatMessage.`Message ID` = message.`Message ID`
            JOIN 
                `message-sender table` AS messageSender ON message.`Message ID` = messageSender.`Message ID`
            JOIN 
                `employee table` AS employee ON messageSender.`Sender ID` = employee.`Employee ID`
            WHERE 
                chatMessage.`Chat ID` = $chatID
            ORDER BY 
                message.`Message Date` ASC,
                message.`Message Time` ASC";

// display employees belonging to a chat
$query72 = "SELECT
                employee.`Employee ID`,
                employee.`Employee First Name`,
                employee.`Employee Last Name`,
                CASE
                    WHEN groupAdmin.`Admin ID` IS NOT NULL THEN 1
                    ELSE 0
                END AS IsAdmin
            FROM
                `employee table` AS employee
            JOIN
                `chat-chat recipients table` AS chatRecipients ON employee.`Employee ID` = chatRecipients.`Recipient ID`
            JOIN
                `group-chat table` AS groupChat ON chatRecipients.`Chat ID` = groupChat.`Chat ID`
            JOIN
                `group-admin` AS groupAdmin ON groupChat.`Group ID` = groupAdmin.`Group ID` AND employee.`Employee ID` = groupAdmin.`Admin ID`
            WHERE
                groupChat.`Group ID` IS NOT NULL
            ORDER BY
                IsAdmin DESC,
                employee.`Employee Last Name` ASC";

// select employee from employees belonging to chat
$query73 = "SELECT
                `Employee First Name`
                `Employee Last Name`
                `Employee Email`
            FROM
                `employee table`
            WHERE
                `Employee ID` = $employeeId";

// block employee
$query74 = "INSERT INTO `employee-block table` (`Blocking Employee ID`, `Blocked Employee ID`)
            VALUES ($currentEmployeeId, $employeeToBlockId)";

// unblock employee
$query75 = "DELETE FROM `employee-block table`
            WHERE `Blocking Employee ID` = $currentEmployeeId
            AND `Blocked Employee ID` = $employeeToUnblockId";

// report employee from message
$query76 = "SELECT `Message Text` 
            FROM `message table` WHERE `Message ID` = $selectedMessageId";
$query77 = "INSERT INTO `report table` (`Report Reason`)
            VALUES (`$messageText`)";
$query78 = "INSERT INTO `reported-employee table` (`Report ID`, `Reported Employee ID`)
            SELECT $reportId, `Sender ID`
            FROM `message-sender table`
            WHERE `Message ID` = $selectedMessageId";
$query79 = "INSERT INTO `Reporting employee table` (`Report ID`, `Reporting Employee ID`)
            VALUES ($reportId, $currentEmployeeId)";

// edit a group
$query80 = "SELECT *
            FROM `group-admin table`
            WHERE `Group ID` = $currentGroupId
            AND `Admin ID` = $currentEmployeeId";
$query81 = "INSERT INTO `group-admin table` (`Group ID`, `Admin ID`)
            VALUES ($current_group_id, $selected_employee_id)";

// report employee without message
$query82 = "INSERT INTO `report table` (`Report Reason`)
            VALUES ('$employeeReportReason')";
$query83 = "INSERT INTO `reported-employee table` (`Report ID`, `Reported Employee ID`)
            VALUES ($reportId, $reportedEmployeeId)";
$query84 = "INSERT INTO `reporting-employee table` (`Report ID`, `Reporting Employee ID`)
            VALUES ($reportId, $currentEmployeeId)";

// search for a chat
$query85 = "SELECT *
            FROM `chat table` AS chat
            JOIN `chat-chat recipients table` AS chatRecipients ON chat.`Chat ID` = chatRecipients.`Chat ID`
            WHERE chat.`Chat Name` LIKE '%$inputChatName%'
            AND chatRecipients.`Chat Recipients ID` = $currentEmployeeId";
/* must be modifyed with early queries if search for a chat within the different categories, i.e. individual chats,
group chats, unread chats, favourite chats*/

// search for message in a chat
$query86 = "SELECT *
            FROM `message table`
            WHERE `Message Text` LIKE '%$inputMessageText%'
            AND `Message ID` IN (
                SELECT `Message ID`
                FROM `chat-message table`
                WHERE `Chat ID` = $currentChatId
            )
            ORDER BY
                `Message Date` ASC, 
                `Message Time` ASC";

// generate notifications when chat's are received
$query87 = "SELECT MAX(`Message ID`) AS recentMessageId
            FROM `message table`";
$query88 = "SELECT `Receiver ID`
            FROM `message-receiver table`
            WHERE `Message ID` = $recentMessageId";
$query89 = "SELECT `Chat ID`
            FROM `chat-message table`
            WHERE `Message ID` = $recentMessageId";
$query90 = "SELECT `Chat Recipients ID`
            FROM `chat-chat recipients table`
            WHERE `Chat ID` = $chatId 
            AND `Chat Muted` = 0";
$query91 = "INSERT INTO `notification table` (`Notification Text`, `Notification Read`) 
            SELECT CONCAT('New message in chat:', $chatId), 0 
            FROM `chat-chat recipients table` 
            WHERE `Chat ID` = $chatId 
            AND `Chat Recipients ID` IN (
                SELECT `Receiver ID` 
                FROM `message-receiver table`
                WHERE `Message ID` = $recentMessageId
            )";
$query92 = "INSERT INTO `notification-chat table` (`Notification ID`, `Chat ID`) 
            SELECT `Notification ID`, $chatId 
            FROM `notification table` 
            WHERE `Notification Text` = CONCAT('New message in chat:', $chatId)";

// read notification
$query93 = "DELETE FROM `notification-chat table`
            WHERE `Notification ID` = $notificationId";
$query94 = "DELETE FROM `notification table`
            WHERE `Notification ID` = $notificationId";
?>