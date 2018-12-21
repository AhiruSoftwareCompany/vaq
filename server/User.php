<?php
class User implements JsonSerializable {
    private $uid;
    private $votes; // array

    public function __construct($json = false, $uid = -1, $votes = null) {
        if ($json) {
            $this->set(json_decode($json, true));
        } else {
            $this->uid = $uid;
            $this->votes = $votes;
        }
    }

    private function set($data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sub = array();
                foreach ($value as $sValue) {
                    array_push($sub, new Vote($json = json_encode($sValue)));
                }
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }

    public function getUID() {
        return $this->uid;
    }

    public function getVotes() {
        return $this->votes;
    }

    public function vote($vote) {
        if ($this->votes === null) {
            $this->votes = array();
        }

        $found = false;
        $diff = 0;
        foreach ($this->votes as $i => $v) {
            if ($v->getId() == $vote->getId()) {
                $diff = $vote->getVote() - $v->getVote();
                $this->votes[$i] = $vote;
                $found = true;
            }
        }
        if (!$found) {
            array_push($this->votes, $vote);
            $diff = $vote->getVote();
        }

        return $diff;
    }

    public function jsonSerialize() {
        return [
            'uid' => $this->getUID(),
            'votes' => $this->getVotes()
        ];
    }
}
?>