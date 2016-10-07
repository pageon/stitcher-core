<?php
/* Smarty version 3.1.30, created on 2016-10-07 15:25:21
  from "/sites/stitcher/stitcher/tests/src/template/home.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_57f7be619bfb44_90762054',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9a6f13efda794c18d7c570c0126f8ce0ffa294e4' => 
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
function content_57f7be619bfb44_90762054 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_184462756557f7be619bdae9_61845184', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_184462756557f7be619bdae9_61845184 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    HELLO
<?php
}
}
/* {/block 'content'} */
}
