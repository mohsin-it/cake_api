<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Chat $chat
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $chat->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $chat->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Chats'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="chats form large-9 medium-8 columns content">
    <?= $this->Form->create($chat) ?>
    <fieldset>
        <legend><?= __('Edit Chat') ?></legend>
        <?php
            echo $this->Form->control('sender_id');
            echo $this->Form->control('receiver_id');
            echo $this->Form->control('message');
            echo $this->Form->control('status');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
