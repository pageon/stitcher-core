<?php
/* Smarty version 3.1.30, created on 2016-10-07 15:31:40
  from "/sites/stitcher/stitcher/tests/src/template/home.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_57f7bfdcaaecf9_73119312',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6ad67b8e54ae4e83f9b21d61064e80f6d7d534c2' => 
    array (
      0 => '/sites/stitcher/stitcher/tests/src/template/home.tpl',
      1 => 1475853797,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index' => 1,
  ),
),false)) {
function content_57f7bfdcaaecf9_73119312 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_148322109357f7bfdcaade28_14608267', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_148322109357f7bfdcaade28_14608267 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    HELLO
<?php
}
}
/* {/block 'content'} */
}
