export class User {
    public name: string; // username
    public pwd: string; // password
    public origins: string[]; // list of legal origins to choose from

    public constructor(name:string, pwd: string, origins?: string[]) {
        this.name = name;
        this.pwd = pwd;
        this.origins = origins;
    }
}
