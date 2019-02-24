import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

import { DataService } from '../../services/data.service';
import { User } from '../../models/user';

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
        private dataService: DataService) {
    }

    ngOnInit() { }

    public login(): void {
        this.dataService.login(new User(this.name, this.pwd))
        .then(result => {
            // TODO save user in context
            this.router.navigate(['/main']);
        })
        .catch(result => {
            this.message = "Login failed!";
        });
    }

    public entering(): void {
        this.message = ''; // Clear error message
    }
}
