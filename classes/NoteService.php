<?php

final class NoteService extends TeachingService
{
    public function topics($actor) {
        return $this->rows('SELECT DISTINCT t.*,s.sbjname,c.classname AS class_name FROM lhpscheme t JOIN lhpalloc a ON a.classid=t.classname AND a.sbjid=t.subject AND a.term=t.term JOIN lhpsubject s ON s.sbjid=a.sbjid JOIN lhpclass c ON c.classid=a.classid WHERE a.staffid=? AND t.term=? AND t.status=1 ORDER BY c.classname,s.sbjname,t.week,t.schmid',array($actor,$this->activeTerm()));
    }
    public function get($id,$actor,$role,$edit=false) {
        $note=$this->one('SELECT n.*,t.topic,t.week,t.classname AS class_id,s.sbjname,c.classname AS class_name,st.staffname FROM lhpnote n JOIN lhpscheme t ON t.schmid=n.topicid JOIN lhpsubject s ON s.sbjid=n.sbjid LEFT JOIN lhpclass c ON c.classid=t.classname LEFT JOIN lhpstaff st ON st.sname=n.staffid WHERE n.noteid=? AND n.status=1',array(self::integer($id,'Note',1)));
        if(!$note) throw new RuntimeException('This note is not available.');
        if($role==='Instructor') {
            $this->allocation($actor,$note['class_id'],$note['sbjid']);
            if($edit && ($note['staffid']!==$actor || $note['term']!==$this->activeTerm())) throw new RuntimeException('Only the author can edit this current-term note.');
        } elseif($role==='Learner' && !$edit) {
            $learner=$this->one('SELECT classid FROM lhpuser WHERE uname=? AND status=1',array($actor));
            if(!$learner || (string)$learner['classid']!==(string)$note['class_id'] || $note['term']!==$this->activeTerm()) throw new RuntimeException('This note is not available for your class.');
        } else throw new RuntimeException('This note is not available for your account.');
        return $note;
    }
    public static function version(array $note) { return hash('sha256',$note['topicid'].'|'.$note['type'].'|'.$note['content'].'|'.$note['status']); }
    public static function safeUrl($value) {
        $value=self::text($value,2000);
        if(!filter_var($value,FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($value,PHP_URL_SCHEME)),array('http','https'),true) || parse_url($value,PHP_URL_USER)!==null) throw new InvalidArgumentException('Use a complete https:// or http:// learning resource link.');
        return $value;
    }
    public static function html($html) {
        $html=self::text($html,500000,true);
        if($html==='') return '';
        $doc=new DOMDocument();$previous=libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);
        libxml_clear_errors();libxml_use_internal_errors($previous);
        $allowed=array('div','p','br','h2','h3','h4','strong','b','em','i','u','s','ul','ol','li','blockquote','table','thead','tbody','tr','th','td','sub','sup','span','a','img','hr','pre','code');
        $nodes=array();foreach($doc->getElementsByTagName('*') as $node) $nodes[]=$node;
        foreach(array_reverse($nodes) as $node) {
            $tag=strtolower($node->nodeName);
            if(!in_array($tag,$allowed,true)) {
                if(in_array($tag,array('script','style','iframe','object','embed','svg','math','form','input','button','textarea'),true)) $node->parentNode->removeChild($node);
                else { while($node->firstChild) $node->parentNode->insertBefore($node->firstChild,$node);$node->parentNode->removeChild($node); }
                continue;
            }
            $href=$node->getAttribute('href');$span=$node->getAttribute('colspan');$src=$node->getAttribute('src');$alt=$node->getAttribute('alt');
            while($node->attributes->length) $node->removeAttributeNode($node->attributes->item(0));
            if($tag==='a' && $href!=='') { try{$node->setAttribute('href',self::safeUrl($href));$node->setAttribute('rel','noopener noreferrer');$node->setAttribute('target','_blank');}catch(InvalidArgumentException $e){} }
            if($tag==='img') {
                $safe=false;
                try{$src=self::safeUrl($src);$safe=true;}catch(InvalidArgumentException $e){
                    $safe=(bool)preg_match('#^(?!//)[a-zA-Z0-9_./% -]+\.(?:png|jpe?g|gif|webp)$#iD',$src);
                    if(!$safe && preg_match('#^data:image/(?:png|jpeg|gif|webp);base64,[A-Za-z0-9+/=]+$#D',$src)) $safe=true;
                }
                if($safe){$node->setAttribute('src',$src);$node->setAttribute('alt',mb_substr($alt,0,250));$node->setAttribute('loading','lazy');$node->setAttribute('referrerpolicy','no-referrer');}
                else{$node->parentNode->removeChild($node);}
            }
            if(in_array($tag,array('td','th'),true) && ctype_digit($span) && (int)$span<=20) $node->setAttribute('colspan',$span);
        }
        $output='';foreach($doc->childNodes as $child) if($child instanceof DOMElement) $output.=$doc->saveHTML($child);
        return $output;
    }
    public function save(array $data,$actor) {
        $id=self::integer($data['id'] ?? 0,'Note');
        $topic=$this->one('SELECT * FROM lhpscheme WHERE schmid=? AND term=? AND status=1',array(self::integer($data['topicid'] ?? 0,'Topic',1),$this->activeTerm()));
        if(!$topic) throw new InvalidArgumentException('Choose a current scheme-of-work topic.');
        $this->allocation($actor,$topic['classname'],$topic['subject']);
        $type=$data['type'] ?? '';
        if(!in_array($type,array('text','online'),true)) throw new InvalidArgumentException('Choose a written note or web resource.');
        $content=$type==='text' ? self::html($data['content'] ?? '') : self::safeUrl($data['weblink'] ?? '');
        if(trim(html_entity_decode(strip_tags($content),ENT_QUOTES,'UTF-8'))==='') throw new InvalidArgumentException('Write some note content before saving.');
        return $this->locked('note:'.($id ?: $actor),function() use($id,$topic,$type,$content,$data,$actor) {
            $old=null;
            if($id) {
                $this->one('SELECT noteid FROM lhpnote WHERE noteid=? FOR UPDATE',array($id));
                $old=$this->get($id,$actor,'Instructor',true);
                if(($data['version'] ?? '')!==self::version($old)) throw new RuntimeException('This note changed in another window. Reload it before saving.');
                if(!in_array($old['type'],array('text','online'),true)) throw new RuntimeException('Existing uploaded notes are read-only in this editor. Add a written note or web resource instead.');
                $this->audit('note',$id,$actor,'edit_requested',$old,array('topicid'=>$topic['schmid'],'type'=>$type,'content'=>$content));
                $this->execute('UPDATE lhpnote SET topicid=?,sbjid=?,type=?,content=? WHERE noteid=?',array($topic['schmid'],$topic['subject'],$type,$content,$id));
            } else {
                $existing=$this->one('SELECT noteid FROM lhpnote WHERE topicid=? AND staffid=? AND type=? AND content=? AND status=1 LIMIT 1',array($topic['schmid'],$actor,$type,$content));
                if($existing) return (int)$existing['noteid'];
                $this->execute('INSERT INTO lhpnote (topicid,sbjid,term,type,content,staffid,status) VALUES (?,?,?,?,?,?,1)',array($topic['schmid'],$topic['subject'],$topic['term'],$type,$content,$actor));
                $id=(int)$this->db->lastInsertId();
            }
            $this->audit('note',$id,$actor,$old?'edit':'create',$old,array('topicid'=>$topic['schmid'],'type'=>$type,'content'=>$content));return $id;
        });
    }
    public function remove($id,$version,$actor,$confirmed) {
        if(!$confirmed) throw new InvalidArgumentException('Confirm removing the note.');
        return $this->locked('note:'.$id,function() use($id,$version,$actor) {
            $this->one('SELECT noteid FROM lhpnote WHERE noteid=? FOR UPDATE',array($id));$note=$this->get($id,$actor,'Instructor',true);
            if($version!==self::version($note)) throw new RuntimeException('The note changed. Refresh before removing it.');
            $this->audit('note',$id,$actor,'archive_requested',$note,null);
            $this->execute('UPDATE lhpnote SET status=0 WHERE noteid=?',array($id));$this->audit('note',$id,$actor,'archive',$note,null);
        });
    }
}
