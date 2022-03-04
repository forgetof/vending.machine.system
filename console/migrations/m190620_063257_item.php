<?php

use yii\db\Migration;

/**
 * Class m190620_063257_item
 */
class m190620_063257_item extends Migration
{
  public function safeUp()
  {
    $this->createTable('item', [
      'id' => $this->primaryKey(),
      'product_id' => $this->integer()->notNull(),
      'box_id' => $this->integer()->notNull(),
      'price' => $this->float(10, 2)->notNull(),
      'status' => $this->smallInteger()->notNull(),
      'data_json' => $this->text(),
      'created_at' => $this->integer()->notNull(),
      'updated_at' => $this->integer()->notNull(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function safeDown()
  {
    $this->dropTable('item');
  }
}
