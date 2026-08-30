<?php

final class InventoryService extends SchoolService
{
    public function items() { return $this->rows('SELECT * FROM school_inventory_items ORDER BY active DESC,name'); }
    public function get($id) { return $this->one('SELECT * FROM school_inventory_items WHERE id=?',array(self::integer($id,'Item',1))); }
    public function history($id) { return $this->rows('SELECT * FROM school_inventory_movements WHERE item_id=? ORDER BY id DESC LIMIT 200',array(self::integer($id,'Item',1))); }
    public function save(array $data, $actor) {
        $values = array(strtoupper(self::text($data['sku'] ?? '',60)),self::text($data['name'] ?? '',160),self::text($data['category'] ?? '',80),
            self::text($data['unit'] ?? '',30),self::text($data['location'] ?? '',120),self::integer($data['minimum_stock'] ?? 0,'Reorder level'),self::money($data['unit_cost'] ?? 0));
        return $this->transaction(function () use ($data,$actor,$values) {
            $id = self::integer($data['id'] ?? 0,'Item'); $before = null;
            if ($this->one('SELECT id FROM school_inventory_items WHERE sku=? AND id<>?',array($values[0],$id))) throw new InvalidArgumentException('That item code is already in use.');
            if ($id) {
                $before = $this->one('SELECT * FROM school_inventory_items WHERE id=? FOR UPDATE',array($id));
                if (!$before || (int)$before['version'] !== (int)($data['version'] ?? 0)) throw new RuntimeException('This item changed. Reload before saving.');
                $this->execute('UPDATE school_inventory_items SET sku=?,name=?,category=?,unit=?,location=?,minimum_stock=?,unit_cost=?,version=version+1 WHERE id=?',array_merge($values,array($id)));
            } else {
                $this->execute('INSERT INTO school_inventory_items (sku,name,category,unit,location,minimum_stock,unit_cost,created_by) VALUES (?,?,?,?,?,?,?,?)',array_merge($values,array($actor)));
                $id = (int)$this->db->lastInsertId();
            }
            $this->audit('inventory',$id,$actor,$before ? 'edit' : 'create',$before,$this->get($id)); return $id;
        });
    }
    public function move(array $data, $actor) {
        $id = self::integer($data['id'] ?? 0,'Item',1); $qty = self::integer($data['quantity'] ?? 0,'Quantity',1,1000000);
        $type = $data['movement_type'] ?? '';
        if (!in_array($type,array('receive','issue','return','write_off'),true)) throw new InvalidArgumentException('Choose a stock movement.');
        $reason = self::text($data['reason'] ?? '',500); $recipient = self::text($data['recipient'] ?? '',160,$type !== 'issue'); $key = self::key($data['request_key'] ?? '');
        return $this->transaction(function () use ($id,$qty,$type,$reason,$recipient,$key,$actor) {
            $item = $this->one('SELECT * FROM school_inventory_items WHERE id=? AND active=1 FOR UPDATE',array($id));
            if (!$item) throw new RuntimeException('This inventory item is not active.');
            if ($this->one('SELECT id FROM school_inventory_movements WHERE request_key=?',array($key))) return;
            $delta = in_array($type,array('issue','write_off'),true) ? -$qty : $qty;
            $balance = (int)$item['quantity'] + $delta;
            if ($balance < 0 || $balance > 1000000000) throw new InvalidArgumentException('There is not enough stock for this movement, or the stock limit would be exceeded.');
            $this->execute('UPDATE school_inventory_items SET quantity=?,version=version+1 WHERE id=?',array($balance,$id));
            $this->execute('INSERT INTO school_inventory_movements (item_id,movement_type,quantity,balance_after,recipient,reason,request_key,created_by) VALUES (?,?,?,?,?,?,?,?)',array($id,$type,$qty,$balance,$recipient,$reason,$key,$actor));
            $this->audit('inventory',$id,$actor,$type,array('quantity'=>$item['quantity']),array('quantity'=>$balance,'reason'=>$reason));
        });
    }
    public function archive($id,$actor,$confirmed) {
        if (!$confirmed) throw new InvalidArgumentException('Confirm archiving this item.');
        return $this->transaction(function () use ($id,$actor) {
            $item = $this->one('SELECT * FROM school_inventory_items WHERE id=? FOR UPDATE',array(self::integer($id,'Item',1)));
            if (!$item || (int)$item['quantity'] !== 0) throw new RuntimeException('Only items with zero stock can be archived.');
            $this->execute('UPDATE school_inventory_items SET active=0,version=version+1 WHERE id=?',array($id));
            $this->audit('inventory',$id,$actor,'archive',$item,null);
        });
    }
}
