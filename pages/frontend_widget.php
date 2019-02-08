<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $item): ?>
            <tr>
                <td><?php echo $item->name ?></td>
                <td><?php echo $item->email ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>