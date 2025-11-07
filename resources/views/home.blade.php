@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="card-title display-4 text-primary">
                    <i class="fas fa-laravel"></i> {{ $title }}
                </h1>
                <p class="card-text lead">{{ $message }}</p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <i class="fas fa-code fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">MVC 架構</h5>
                                <p class="card-text">使用 Model-View-Controller 設計模式</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <i class="fas fa-database fa-3x text-success mb-3"></i>
                                <h5 class="card-title">資料庫整合</h5>
                                <p class="card-text">Eloquent ORM 和資料庫遷移</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <i class="fas fa-route fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">路由系統</h5>
                                <p class="card-text">RESTful API 和 Web 路由</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-users"></i> 查看用戶管理
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-info-circle"></i> 了解更多
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
