@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-info-circle"></i> {{ $title }}
                </h2>
            </div>
            <div class="card-body">
                <p class="lead">{{ $description }}</p>
                
                <h4 class="mt-4">Laravel 框架特色</h4>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>Eloquent ORM</strong> - 優雅的資料庫操作
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>Blade 模板引擎</strong> - 強大的視圖系統
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>Artisan 命令列</strong> - 開發工具集
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>路由系統</strong> - 靈活的路由配置
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>中介軟體</strong> - 請求過濾和處理
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>資料庫遷移</strong> - 版本控制資料庫結構
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>表單驗證</strong> - 內建驗證機制
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check text-success"></i> 
                                <strong>快取系統</strong> - 多種快取驅動
                            </li>
                        </ul>
                    </div>
                </div>

                <h4 class="mt-4">專案結構</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-folder text-primary"></i> app/
                                </h6>
                                <small class="text-muted">
                                    應用程式核心邏輯<br>
                                    • Controllers<br>
                                    • Models<br>
                                    • Middleware
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-folder text-success"></i> resources/
                                </h6>
                                <small class="text-muted">
                                    視圖和前端資源<br>
                                    • views/<br>
                                    • css/<br>
                                    • js/
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-folder text-warning"></i> database/
                                </h6>
                                <small class="text-muted">
                                    資料庫相關文件<br>
                                    • migrations/<br>
                                    • seeders/<br>
                                    • factories/
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home"></i> 返回首頁
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
