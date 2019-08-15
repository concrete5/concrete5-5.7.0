<?php
namespace Concrete\Core\Permission\Response;
use Concrete\Core\File\Filesystem;
use Concrete\Core\User\User;
use Concrete\Core\File\Set\Set as FileSet;

/**
 * @deprecated
 */
class FileSetResponse extends Response {

    protected $permissions;

    public function __call($nm, $args)
    {
        if (!isset($this->permissions)) {
            $filesystem = new Filesystem();
            $this->permissions = new \Concrete\Core\Permission\Checker($filesystem->getRootFolder());
        }

        return call_user_func_array(array($this->permissions, $nm), $args);
    }


}
