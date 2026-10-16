<!DOCTYPE html>
<html>
<head>
    <title>Error <?= htmlspecialchars($error['code'] ?? 500) ?></title>
    <style>
        body { 
            background: #1e1e1e; 
            color: #eee;
            font-family: Consolas, monospace;
            padding: 30px;
        }
        .box {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        h1 { color: #ff5555; }
        table td { padding: 8px 10px; vertical-align: top; }
        .key { color: #79c0ff; width: 180px; }
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: #111;
            padding: 10px;
            border-radius: 5px;
            color: #7ce8a0;
        }
        .error-line {
            background: #552222;
            color: #ffaaaa;
            padding: 2px 4px;
        }
    </style>
</head>
<body>

    <h1>Error <?= htmlspecialchars($error['code'] ?? 500) ?></h1>

    <div class="box">
        <table>
            <tr>
                <td class="key">Message</td>
                <td><?= htmlspecialchars($error['message'] ?? 'No message') ?></td>
            </tr>

            <tr>
                <td class="key">File</td>
                <td><?= htmlspecialchars($error['file'] ?? '-') ?></td>
            </tr>

            <tr>
                <td class="key">Line</td>
                <td><?= htmlspecialchars($error['line'] ?? '-') ?></td>
            </tr>

            <tr>
                <td class="key">View Requested</td>
                <td><?= htmlspecialchars($error['view_requested'] ?? '-') ?></td>
            </tr>

            <tr>
                <td class="key">Time</td>
                <td><?= htmlspecialchars($error['time'] ?? '-') ?></td>
            </tr>

            <?php if (!empty($error['render_error'])): ?>
            <tr>
                <td class="key">Render Error</td>
                <td><pre><?= htmlspecialchars($error['render_error']) ?></pre></td>
            </tr>
            <?php endif; ?>

            <tr>
                <td class="key">GET</td>
                <td><pre><?= htmlspecialchars(json_encode($error['get'] ?? $_GET, JSON_PRETTY_PRINT)) ?></pre></td>
            </tr>

            <tr>
                <td class="key">POST</td>
                <td><pre><?= htmlspecialchars(json_encode($error['post'] ?? $_POST, JSON_PRETTY_PRINT)) ?></pre></td>
            </tr>

            <tr>
                <td class="key">SESSION</td>
                <td><pre><?= htmlspecialchars(json_encode($error['session'] ?? $_SESSION, JSON_PRETTY_PRINT)) ?></pre></td>
            </tr>
        </table>
    </div>

    <!-- CODE SNIPPET -->
    <?php if (!empty($error['snippet'])): ?>
    <div class="box">
        <h3>Code Snippet (around line <?= htmlspecialchars($error['line']) ?>)</h3>
        <pre><?= htmlspecialchars($error['snippet']) ?></pre>
    </div>
    <?php endif; ?>

    <!-- FULL ERROR ARRAY -->
    <div class="box">
        <h3>Full Error Array</h3>
        <pre><?= htmlspecialchars(print_r($error, true)) ?></pre>
    </div>

</body>
</html>
<?php /**PATH C:\laragon\www\myinternship\views/errors/error_debug.blade.php ENDPATH**/ ?>