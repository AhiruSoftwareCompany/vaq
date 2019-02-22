import { Component } from '@angular/core';

@Component({
    selector: 'app-root',
    template: ` <div class="outer-box">
                    <router-outlet></router-outlet>
                </div>`,
    styleUrls: ['./app.component.css']
})

export class AppComponent {
}
