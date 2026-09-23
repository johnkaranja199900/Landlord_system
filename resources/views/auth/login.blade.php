<x-layout title="Login">
    <div class="card" style="max-width:420px;margin:3rem auto;">
        <h2>Sign in</h2>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label>Email</label><input type="email" name="email" value="{{ old('email') }}" required>
            <label>Password</label><input type="password" name="password" required>
            <label><input type="checkbox" name="remember" style="width:auto"> Remember me</label>
            <button class="btn">Login</button>
        </form>
    </div>
</x-layout>
