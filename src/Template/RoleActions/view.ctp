<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleAction $roleAction
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Role Action'), ['action' => 'edit', $roleAction->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Role Action'), ['action' => 'delete', $roleAction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $roleAction->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Role Actions'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Role Action'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Actions'), ['controller' => 'Actions', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Action'), ['controller' => 'Actions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="roleActions view large-9 medium-8 columns content">
    <h3><?= h($roleAction->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Role') ?></th>
            <td><?= $roleAction->has('role') ? $this->Html->link($roleAction->role->id, ['controller' => 'Roles', 'action' => 'view', $roleAction->role->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Action') ?></th>
            <td><?= $roleAction->has('action') ? $this->Html->link($roleAction->action->id, ['controller' => 'Actions', 'action' => 'view', $roleAction->action->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($roleAction->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Allowed') ?></th>
            <td><?= $roleAction->is_allowed ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
