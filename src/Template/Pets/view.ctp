<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Pet $pet
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Pet'), ['action' => 'edit', $pet->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Pet'), ['action' => 'delete', $pet->id], ['confirm' => __('Are you sure you want to delete # {0}?', $pet->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Pets'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Pet'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Breeds'), ['controller' => 'Breeds', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Breed'), ['controller' => 'Breeds', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Profiles'), ['controller' => 'Profiles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Profile'), ['controller' => 'Profiles', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="pets view large-9 medium-8 columns content">
    <h3><?= h($pet->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($pet->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($pet->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($pet->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($pet->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Breeds') ?></h4>
        <?php if (!empty($pet->breeds)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Pet Id') ?></th>
                <th scope="col"><?= __('Name') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($pet->breeds as $breeds): ?>
            <tr>
                <td><?= h($breeds->id) ?></td>
                <td><?= h($breeds->pet_id) ?></td>
                <td><?= h($breeds->name) ?></td>
                <td><?= h($breeds->created) ?></td>
                <td><?= h($breeds->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Breeds', 'action' => 'view', $breeds->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Breeds', 'action' => 'edit', $breeds->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Breeds', 'action' => 'delete', $breeds->id], ['confirm' => __('Are you sure you want to delete # {0}?', $breeds->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Profiles') ?></h4>
        <?php if (!empty($pet->profiles)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Uniq Id') ?></th>
                <th scope="col"><?= __('Owner Id') ?></th>
                <th scope="col"><?= __('Pet Id') ?></th>
                <th scope="col"><?= __('Breed Id') ?></th>
                <th scope="col"><?= __('Size') ?></th>
                <th scope="col"><?= __('Gender') ?></th>
                <th scope="col"><?= __('Age') ?></th>
                <th scope="col"><?= __('Color') ?></th>
                <th scope="col"><?= __('Neutered') ?></th>
                <th scope="col"><?= __('Intact') ?></th>
                <th scope="col"><?= __('Mix') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($pet->profiles as $profiles): ?>
            <tr>
                <td><?= h($profiles->id) ?></td>
                <td><?= h($profiles->uniq_id) ?></td>
                <td><?= h($profiles->owner_id) ?></td>
                <td><?= h($profiles->pet_id) ?></td>
                <td><?= h($profiles->breed_id) ?></td>
                <td><?= h($profiles->size) ?></td>
                <td><?= h($profiles->gender) ?></td>
                <td><?= h($profiles->age) ?></td>
                <td><?= h($profiles->color) ?></td>
                <td><?= h($profiles->neutered) ?></td>
                <td><?= h($profiles->intact) ?></td>
                <td><?= h($profiles->mix) ?></td>
                <td><?= h($profiles->created) ?></td>
                <td><?= h($profiles->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Profiles', 'action' => 'view', $profiles->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Profiles', 'action' => 'edit', $profiles->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Profiles', 'action' => 'delete', $profiles->id], ['confirm' => __('Are you sure you want to delete # {0}?', $profiles->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
