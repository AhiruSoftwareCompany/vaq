<?php

class Vote implements JsonSerializable {
    private $id;
    private $vote; // int

    public function __construct($json = false, $id = -1, $vote = 0) {
        if ($json) {
            $this->set(json_decode($json, true));
        } else {
            $this->id = $id;
            $this->vote = $vote;
        }
    }

    private function set($data) {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getVote() {
        return $this->vote;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->getId(),
            'vote' => $this->getVote()
        ];
    }
}
