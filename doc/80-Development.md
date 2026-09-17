# Development

A short overview of the TLV classes and their roles.

`ViewConfig` is responsible for managing the YAML files that contain
the views.

```
$c = new ViewConfig($modules_config_dir)
$views = $c->loadAll();
$view = $c->loadByName('myview');
```

This class also handles storing/loading the view data into either YAML files
or the user's session.

`Model\View` represents a single Top Level View and is
responsible for parsing the YAML data.

It also contains the tree data structure representing the view's hierarchy.
The `getTree()` method will return this tree.

`Tree\TLVTree` represents the root of the TLV tree.
It store and load itself from the Icinga Web `FileCache` via the `storeCache()/loadCache()` methods.

`Tree\TLVTreeNode` represents a node in the TLV tree.

Each node has an numeric ID based on its position in the tree (example: 9, 9-0, 9-1, 9-0-1).
This ID is also used in HTML links to show a subtree.

This class is used in recursively rendering the tree into HTML.

`Tree\TLVIcingaNode` is a tree node that can fetch Icinga data from the database.

Each of the classes `Tree\TLVHostGroupNode`, `Tree\TLVHostNode`, `Tree\TLVServiceNode` extends this class.

Each node uses the `fetch()` method to retrieve its data from the database

They use `Tree\TLVStatus` to represent their current status, which is determined with `getStatus()` method of each node.
