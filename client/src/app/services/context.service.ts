import { Injectable } from '@angular/core';

import { User } from '../models/user';

@Injectable({
    providedIn: 'root'
})
export class ContextService {
    private user: User;

    public constructor() {
        this.user = null;
    }

    public getUser(): User {
        return this.user;
    }

    public setUser(user: User): void {
        this.user = user;
    }
}
