<?php

session_start();
header('Content-Type: application/json');
require_once __DIR__.'/../../config/db.php';

global $conn;

if (! isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;

        if ($start_date && $end_date) {
            $sql = 'SELECT e.event_id, e.med_id, e.event_datetime, m.med_name 
                    FROM calendar_events e
                    JOIN med m ON e.med_id = m.med_id
                    WHERE e.user_id = ? AND e.event_datetime BETWEEN ? AND ?
                    ORDER BY e.event_datetime ASC';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iss', $user_id, $start_date, $end_date);
        } else {
            $sql = 'SELECT e.event_id, e.med_id, e.event_datetime, m.med_name 
                    FROM calendar_events e
                    JOIN med m ON e.med_id = m.med_id
                    WHERE e.user_id = ?
                    ORDER BY e.event_datetime ASC';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $events]);
        break;

    case 'POST':
        $med_id = $_POST['med_id'] ?? null;
        $datetime = $_POST['event_datetime'] ?? null;

        if (! $med_id || ! $datetime) {
            echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
            exit;
        }

        $sql = 'INSERT INTO calendar_events (user_id, med_id, event_datetime) VALUES (?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $user_id, $med_id, $datetime);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Event added', 'event_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add event']);
        }
        break;

    case 'PUT':
        parse_str(file_get_contents('php://input'), $put_vars);
        $event_id = $put_vars['event_id'] ?? null;
        $datetime = $put_vars['event_datetime'] ?? null;
        $med_id = $put_vars['med_id'] ?? null;

        if (! $event_id || ! $datetime || ! $med_id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
            exit;
        }

        $sql = 'UPDATE calendar_events SET event_datetime = ?, med_id = ? WHERE event_id = ? AND user_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('siii', $datetime, $med_id, $event_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Event updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update event']);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $del_vars);
        $event_id = $del_vars['event_id'] ?? null;

        if (! $event_id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing event_id']);
            exit;
        }

        $stmt = $conn->prepare('SELECT med_id FROM calendar_events WHERE event_id = ? AND user_id = ?');
        $stmt->bind_param('ii', $event_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            exit;
        }

        $row = $result->fetch_assoc();
        $med_id = $row['med_id'];
        $stmt->close();

        $stmt = $conn->prepare('SELECT reminder_id FROM reminder WHERE med_id = ? AND user_id = ?');
        $stmt->bind_param('ii', $med_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reminder_row = $result->fetch_assoc();
            $reminder_id = $reminder_row['reminder_id'];
            $stmt->close();

            $stmt = $conn->prepare('DELETE FROM reminder_times WHERE reminder_id = ?');
            $stmt->bind_param('i', $reminder_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare('DELETE FROM reminder WHERE reminder_id = ?');
            $stmt->bind_param('i', $reminder_id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $conn->prepare('DELETE FROM calendar_events WHERE event_id = ? AND user_id = ?');
        $stmt->bind_param('ii', $event_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Event and reminder deleted']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete event']);
        }

        $stmt->close();
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}

$conn->close();
