FROM alpine:3.12.4

RUN \
# ==================================== Initial setup ====================================
apk add --update-cache libgcc libstdc++ && \

apk add --virtual build-dependencies \
    autoconf \
    automake \
    bash \
    binutils \
    bison \
    bzip2 \
    flex \
    g++ \
    gawk \
    gdk-pixbuf \
    gettext \
    gettext-dev \
    git \
    gperf \
    help2man \
    intltool \
    libtool \
    linux-headers \
    lzip \
    make \
    curl \
    ncurses-dev \
    openssl \
    openssl-dev \
    p7zip \
    patch \
    perl \
    python3 \
    rsync \
    ruby \
    texinfo \
    unzip \
    wget \
    xz \
    zlib && \

mkdir -p /opt && \
cd /opt && \

# ==================================== Setup Windows corss compilers ====================================
ln -s /usr/bin/python3 /usr/bin/python && \

git clone https://github.com/mxe/mxe.git && \
cd mxe && \
git checkout 29bdf5b0692e1032eb1aa648f39a22f923a3d29d && \

echo -e "\
MXE_TARGETS := x86_64-w64-mingw32.shared i686-w64-mingw32.shared \n\
MXE_USE_CCACHE := \n\
MXE_PLUGIN_DIRS := plugins/gcc10 \n\
LOCAL_PKG_LIST := cc cmake \n\
.DEFAULT local-pkg-list: \n\
local-pkg-list: \$(LOCAL_PKG_LIST) \
" > settings.mk && \

make JOBS=$(nproc) && \

ls | grep -v usr | xargs rm -rf && \
cd .. && \

# ==================================== Setup Linux corss compilers ====================================
# curl -LO http://crosstool-ng.org/download/crosstool-ng/crosstool-ng-1.24.0.tar.bz2 && \
# tar -xjvf crosstool-ng-1.24.0.tar.bz2 && \
# cd crosstool-ng-1.24.0 && \

# ./configure --enable-local && \
# make && \

# for i in *.config; do
#     mv $i .config  # all copies previously

#     echo -e "\
#     CT_EXPERIMENTAL=y" \n\
#     CT_ALLOW_BUILD_AS_ROOT=y" \n\
#     CT_ALLOW_BUILD_AS_ROOT_SURE=y" \
#     " >> .config

#     ./ct-ng build.$(nproc)

#     rm .config
# done && \

# ./ct-ng build.$(nproc)

# cd .. && \
# rm -rf crosstool-ng-1.24.0{,.tar.bz2} && \

# ==================================== Cleanup ====================================
apk del build-dependencies && \
rm -rf /var/cache/apk/*