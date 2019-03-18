import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

import { DataService } from '../../services/data.service';
import { User } from '../../models/user';
import { ContextService } from '../../services/context.service';

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
    public name: string; // the entered username
    public pwd: string; // the entered password
    public message: string; // for error-messages

    constructor(
        private router: Router,
        private dataService: DataService,
        private context: ContextService) {
    }

    ngOnInit() {
        this.dataService.login()
        .then(result => {
            this.message = "FOO";
            this.context.setUser(result);
            this.router.navigate(['/main']);
        });
    }

    public login(): void {
        this.dataService.login(new User(this.name, this.pwd))
        .then(result => {
            this.context.setUser(result);
            this.router.navigate(['/main']);
        })
        .catch(result => {
            this.message = "Login failed!";
        });
    }

    public entering(event): void {
        if (event.key !== "Enter")
            this.message = ''; // Clear error message
    }
}
