<?php
    $results = json_decode($_POST['results']);
?>

<div class="results-tables">

    <h1>Issues Submitted:</h1>
    <?php if ($results->submitted): ?>

    <table border="1" class="success-table">
            <tr class="header">
                <th>Ticket</th>
                <th>Description</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Type</th>
            </tr>

            <?php foreach ($results->submitted as $submission): ?>
                <tr>
                    <td><?php echo $submission->ticket ?></td>
                    <td><?php echo $submission->description ?></td>
                    <td><?php echo $submission->duration ?></td>
                    <td><?php echo $submission->date ?></td>
                    <td><?php echo $submission->type ?></td>
                </tr>
            <?php endforeach;?>

    </table>
    <?php else: ?>
        <?php echo 'No time was imported'?>
    <?php endif; ?>

    <h1>Failed to Submit:</h1>
    <?php if ($results->failed): ?>

    <table border="1" class="failure-table">

            <tr class="header">
                <th>Ticket</th>
                <th>Description</th>
                <th>Duration</th>
                <th>Date</th>
                <th>Type</th>
                <th>Error</th>
            </tr>

            <?php foreach ($results->failed as $failure): ?>
                <tr>
                    <td><?php echo $failure->ticket ?></td>
                    <td><?php echo $failure->description ?></td>
                    <td><?php echo $failure->duration ?></td>
                    <td><?php echo $failure->date ?></td>
                    <td><?php echo $failure->type ?></td>
                    <td class="error"><?php echo $failure->error ?></td>
                </tr>
            <?php endforeach;?>

    </table>
    <?php else: ?>
        <?php echo 'All time submitted successfully'?>
    <?php endif; ?>
</div>