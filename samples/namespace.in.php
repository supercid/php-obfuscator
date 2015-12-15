<?php

namespace Test;

use My\Controller;
use My\Exception;
use My\Serializable as JsonSerializable;
use My\Model;
use My\Model\User;
use My\Model\Comment;
use My\Model\Exception as ModelException;

class MyController extends Controller implements \Countable, JsonSerializable
{
    public function postAction($id)
    {
        if (!$id) {
            // Normal PHP exception.
            throw new \Exception("No ID specified.");
        }

        $user = User::findById($id);
        $profile = Model\Profile::findByUser($user);

        if (!$user || !$profile) {
            throw new ModelException("No user or profile found.");
        }

        $comment = new Comment($user, $profile, "Hello!");
        $comment->save();

        if ($comment->getErrors()) {
            // Imported exception.
            throw new Exception("It failed.");
        }

        return $comment->getId();
    }

    public function count()
    {
        return Comment::count();
    }

    public function serialize()
    {
        return json_encode($this);
    }
}
