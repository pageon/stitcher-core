<?php
/* Smarty version 3.1.30, created on 2016-10-07 15:42:56
  from "/sites/stitcher/stitcher/tests/src/template/home.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_57f7c2807d2d50_74293800',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6c856518ef21dda79b830f7062f84d45aacdc658' => 
    array (
      0 => '/sites/stitcher/stitcher/tests/src/template/home.tpl',
      1 => 1475854975,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:index.tpl' => 1,
  ),
),false)) {
function content_57f7c2807d2d50_74293800 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_129065347257f7c2807d2191_82098578', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender("file:index.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, false);
}
/* {block 'content'} */
class Block_129065347257f7c2807d2191_82098578 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    HELLO
<?php
}
}
/* {/block 'content'} */
}
