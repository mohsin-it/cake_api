<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\RoleAction $roleAction
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $roleAction->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $roleAction->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Role Actions'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Roles'), ['controller' => 'Roles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Role'), ['controller' => 'Roles', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Actions'), ['controller' => 'Actions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Action'), ['controller' => 'Actions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="roleActions form large-9 medium-8 columns content">
    <?= $this->Form->create($roleAction) ?>
    <fieldset>
        <legend><?= __('Edit Role Action') ?></legend>
        <?php
            echo $this->Form->control('role_id', ['options' => $roles]);
            echo $this->Form->control('action_id', ['options' => $actions]);
            echo $this->Form->control('is_allowed');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
