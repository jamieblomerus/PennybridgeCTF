#include <stdbool.h>
#include <stdio.h>

int main() {
    struct {
        char password[256];
        bool is_admin;
    } user;
    const char *flag = "PBCTF{f4k3_fl4g}";

    user.is_admin = false;

    setvbuf(stdout, NULL, _IONBF, 0);
    setvbuf(stdin, NULL, _IONBF, 0);

    puts("Skriv in admin lösenordet:");
    gets(user.password);

    if (user.is_admin) {
        puts("Välkommen admin! Här är flaggan:");
        puts(flag);
    } else {
        puts("Som sagt, det finns inget korrekt lösenord. Du slösade bara din tid.");
    }

    return 0;
}