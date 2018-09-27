<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Chat $chat
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Chat'), ['action' => 'edit', $chat->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Chat'), ['action' => 'delete', $chat->id], ['confirm' => __('Are you sure you want to delete # {0}?', $chat->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Chats'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Chat'), ['action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="chats view large-9 medium-8 columns content">
    <h3><?= h($chat->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($chat->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sender Id') ?></th>
            <td><?= $this->Number->format($chat->sender_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Receiver Id') ?></th>
            <td><?= $this->Number->format($chat->receiver_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $this->Number->format($chat->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($chat->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($chat->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Message') ?></h4>
        <?= $this->Text->autoParagraph(h($chat->message)); ?>
    </div>
</div>
