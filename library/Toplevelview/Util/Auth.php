<?php

namespace Icinga\Module\Toplevelview\Util;

use Icinga\Authentication\Auth as IcingaAuth;
use Icinga\Security\SecurityException;
use ipl\Stdlib\Filter;
use ipl\Stdlib\Str;

/**
 * Auth adds methods for handling permissions/restrictions
 */
trait Auth
{
    public function getAuth(): IcingaAuth
    {
        return IcingaAuth::getInstance();
    }

    /**
     * assertAccessToView asserts that the current user has permission
     * for the given view. Throws a SecurityException if not.
     *
     * @throws SecurityException
     */
    public function assertAccessToView($restrictions, $name): bool
    {
        $user = $this->getAuth()->getUser();

        // If the user is unrestricted no restrictions apply
        if ($user->isUnrestricted()) {
            return true;
        }

        if ($restrictions->isEmpty()) {
            return true;
        }

        if (Filter::match($restrictions, ['name' => $name])) {
            return true;
        }

        throw new SecurityException('No permission for %s', $name);
    }

    /**
     * assertAccessToView asserts that the current user has permission
     * for the given view. Returns false if not
     */
    public function hasAccessToView($restrictions, string $name): bool
    {
        $user = $this->getAuth()->getUser();

        // If the user is unrestricted no restrictions apply
        if ($user->isUnrestricted()) {
            return true;
        }

        if ($restrictions->isEmpty()) {
            return true;
        }

        return Filter::match($restrictions, ['name' => $name]);
    }

    /**
     * getRestrictions returns the current user's restrictions.
     */
    public function getRestrictions(string $name = 'toplevelview/filter/views')
    {
        $user = $this->getAuth()->getUser();

        // The final filter that is applied to the query.
        // Any means any filter has to match
        $f = Filter::any();

        // For each of the user's roles add the given restrictions
        foreach ($user->getRoles() as $role) {
            // All means ALL filters have to match
            $roleFilter = Filter::any();
            // Load the restrictions for the user's role and parse them into a filter
            $restriction = $role->getRestrictions($name);

            if ($restriction) {
                // Parse the given restriction and return a Filter\Rule
                $res = Str::trimSplit($restriction);
                foreach ($res as $r) {
                    // Add the new Rule to the role filter
                    $roleFilter->add(Filter::equal('name', $r));
                }
            }
            // Add the filter to the overall filter
            if (! $roleFilter->isEmpty()) {
                $f->add($roleFilter);
            }
        }

        return $f;
    }
}
